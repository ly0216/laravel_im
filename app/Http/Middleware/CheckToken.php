<?php

namespace App\Http\Middleware;

use App\Common\Code;
use Closure;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                // 用户没有找到
                return response()->json(['code' => Code::TOKEN_ERROR, 'message' => '无效的用户']);
            }
        } catch (TokenExpiredException $e) {
            // token 过期
            return response()->json(['code' => Code::TOKEN_ERROR, 'message' => 'Token过期']);
        } catch (TokenInvalidException $e) {
            // token 无效
            return response()->json(['code' => Code::TOKEN_ERROR, 'message' => '无效的Token']);
        } catch (JWTException $e) {
            // 缺少 token
            return response()->json(['code' => Code::TOKEN_ERROR, 'message' => '缺少Token']);
        }

        return $next($request);
    }
}
