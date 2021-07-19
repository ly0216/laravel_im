<?php

namespace App\Http\Controllers;

use App\Common\Code;
use App\Mongodb\ChatGroupLabel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


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


        try {
            $credentials = $request->only('user_name', 'password');
           /* User::where('id',1)->update([
                'password'=>bcrypt('123456')
            ]);*/
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['code' => 1, 'message' => '用户名或密码错误']);
            }
            return $this->respondWithToken($token);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }


    }

    public function test(Request $request)
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9saXkuaW0uY29tXC9hcGlcL2ltXC9jb252ZXJzYXRpb25cL2NyZWF0ZSIsImlhdCI6MTYyNDY3MTc3MywiZXhwIjoxNjI0NzU4MTczLCJuYmYiOjE2MjQ2NzE3NzMsImp0aSI6InFoZGxkRWJYY1V4UHRrcGwiLCJzdWIiOjEzNDA4LCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.N5g3DGzgD7IKuHbzO6z4L40cJV3zzB_w5jnLLIrkf5U';

        try {

            /*$request->headers->set('Authorization', 'Bearer ' .$token);
            $user = JWTAuth::setToken($token)->parseToken()->authenticate();
            if (!$user) {
                return response()->json(['user_not_found'], 404);
            }
            return response()->json(['code' => 1, 'message' => $user->id    ]);*/
            $idx = $request->get('idx') ?: 0;
            $user_list = ['13483', '13473', '13471', '13467', '13465', '13408'];
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
            $user = auth('api')->user();
            unset($user->created_at);
            unset($user->updated_at);
            unset($user->email);
            unset($user->email_verified_at);
            return response()->json(
                [
                    'code' => Code::HTTP_SUCCESS,
                    'message' => 'success',
                    'data' => $user
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
