<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Impl\AuthService;
use App\Services\Impl\TokenService;
use App\Services\Impl\UserService;
use App\Services\AuthService as AuthServiceInterface;
use App\Services\TokenService as TokenServiceInterface;
use App\Services\UserService as UserServiceInterface;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(TokenServiceInterface::class, TokenService::class);
        $this->app->singleton(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
