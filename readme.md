### 一、安装jwt
1、修改`composer.json`文件,引入jwt
````
"require-dev": {
    ···················
    ···················
    "tymon/jwt-auth": "1.0.*@beta"
},
````
2、更新依赖包资源
````
composer update
````
3、注册jwt的服务提供者和门面,修改config目录里的`app.php`
````
# 服务提供者
'providers' => [
    ······
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
],
#门面
'aliases' => [
    ······
    'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class
],
````
4、发布配置文件
````
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
````
5、生成对称加密的script
````
php artisan jwt:secret
````
### 二、添加jwt认证的数据源和认证方式
1、添加数据源
````  
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    ·····
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->id;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'type' => 'jwt'
        ];
    }
}
````
2、定义认证方式
````
<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/5/18
 * Time: 下午6:00
 */

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use Psy\Util\Str;


class JwtUserServiceProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        
    }
}
````
3、注册认证方式,修改app/Providers/AuthServiceProvider.php
````
public function boot()
{
    $this->registerPolicies();

    Auth::extend('jwt', function($app, $name, array $config) {
        // Return an instance of Illuminate\Contracts\Auth\Guard...
        return new JWTGuard($app['tymon.jwt'],
            new JwtUserServiceProvider(config('auth.user_model')),
            $app['request']);
    });
}
````
4、修改配置文件引入jwt认证,修改config/auth.php
````
'guards' => [
    ·····
    'jwt' => [
        'driver' => 'jwt',
        'provider' => 'jwt',
    ],
],
'providers' => [
    ······
    'jwt' => [
        'driver' => 'eloquent',
        'model' => \App\User::class,
    ],
],
'user_model' => 'App\User',
````
### 三、定义解析token的中间件
1、生成中间件
````
php artisan make:middleware JwtToken
````
2、修改中间件
````
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
````
3、注册中间件,修改`app/Http/Kernel.php`
````
protected $middlewareGroups = [
    ····
    'jwt_api' => [
        'throttle:60,1',
        'jwt.auth',
    ],
];
protected $routeMiddleware = [
    ····
    'jwt.auth' => \App\Http\Middleware\JwtToken::class
];
````
### 四、调试
1、获取token
````
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
````
2、解析token获取用户信息
````
# 这里路由要使用之前定义的中间件
# Route::post('/getUserInfo', 'JwtController@getUserInfo')->middleware('jwt_api');
public function getUserInfo()
{
    dd(Auth::user());
}
````