<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Impl\UserRepository;
use App\Repositories\UserRepository as UserRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
