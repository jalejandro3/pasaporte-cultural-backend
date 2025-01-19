<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Impl\ActivityRepository;
use App\Repositories\Impl\UserRepository;
use App\Repositories\ActivityRepository as ActivityRepositoryInterface;
use App\Repositories\UserRepository as UserRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ActivityRepositoryInterface::class, ActivityRepository::class);
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
