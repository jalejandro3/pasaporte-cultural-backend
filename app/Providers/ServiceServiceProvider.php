<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Impl\AuthService;
use App\Services\AuthService as AuthServiceInterface;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
