<?php

namespace App\Providers;

use App\Domain\Participation\ParticipationRepository;
use App\Infrastructure\Participation\EloquentParticipationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ParticipationRepository::class, EloquentParticipationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
