<?php

namespace App\Http\Controllers;

use App\Mongodb\ChatGroupLabel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\TestFixture\C;

class LoginController extends Controller
{
    //

    public $timestamps = false;
    protected $table = 'user';

    public function __construct()
    {

        $this->middleware('check.token', ['except' => [
            'login', 'test'
        ]]);
    }

    /**
     * 用户登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $mobile = $request->post('mobile');
        $token = $request->post('access_token');
        if (!$mobile || !$token) {
            return response()->json(['code' => 1, 'message' => '参数错误！']);
        }
        try {
            $user = User::where('mobile', $mobile)->where('access_token', $token)->first();
            if (!$user) {
                return response()->json(['code' => 1, 'message' => '无效的用户！']);
            }
            if (!$token = auth('api')->fromUser($user)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }


    }

    public function test(Request $request)
    {
        try {
            $idx = $request->get('idx')?:0;
            $user_list = ['13483', '13473', '13471', '13467', '13465','13408'];
            $user_id = $user_list[$idx];
            return view('login.test')->with([
                'user_id' => strval($user_id)
            ]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $list = ChatGroupLabel::all();
            return response()->json(
                [
                    'code' => 0,
                    'message' => 'success',
                    'data' => [
                        'user' => auth('api')->id(),
                        'list' => $list
                    ]
                ]
            );
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => 'fail', 'data' => $exception->getMessage()]);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json(['code' => 0, 'message' => 'success', 'data' => [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]]);
    }


}
