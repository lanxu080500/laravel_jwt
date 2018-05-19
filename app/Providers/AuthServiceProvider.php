<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\JWTGuard;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
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
}
