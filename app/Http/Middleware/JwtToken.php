<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Util\AuthUtil;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JwtToken
{
    /**
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (! $token = Auth::setRequest($request)->getToken()) {
            Log::error('[Auth][Api] no token in request[' . json_encode($request) . ']');
            throw new ApiException(1001, 'token未提供', array(), 401);
        }
        Log::info('token: ' . $token);
        try {
            $payload = AuthUtil::checkOrFail('jwt');
        } catch (TokenExpiredException $e) {
            throw new ApiException(1002, 'token失效', array(), 401);
        } catch (JWTException $e) {
            throw new ApiException(1003, 'token无效', array(), 401);
        }

        if (! $payload) {
            throw new ApiException(1006, '用户不存在', array(), 404);
        }

        return $next($request);
    }
}
