<?php
/**
 * Created by PhpStorm.
 * User: billy
 * Date: 13/04/2017
 * Time: 2:22 PM
 */

namespace App\Util;


use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthUtil
{
    public static function checkOrFail($guard)
    {
        $payload = Auth::guard($guard)->checkOrFail();
        if ($payload)
        {
            $payload = $payload->getClaims()->toPlainArray();

            if (($payload['type'] == 'customer' && $guard == 'adminApi') || ($payload['type'] == 'admin' && $guard == 'api')){
                throw new JWTException();
            }
        }

        return $payload;
    }
}