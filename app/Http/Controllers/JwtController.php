<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('name', 'password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = Auth::guard('jwt')->attempt($credentials)) {
                throw new ApiException(1008, '登录失败');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            throw new ApiException(1009, 'token创建失败');
        }

        $expire = Auth::guard('jwt')->setToken($token)->getPayload()->get('exp');
        $token = 'Bearer '.$token;

        return compact('token', 'expire');

    }

    public function getUserInfo()
    {
        dd(Auth::user());
    }
}
