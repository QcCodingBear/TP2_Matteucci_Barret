<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('App\Repository\UserRepositoryInterface', 'App\Repository\Eloquent\UserRepository');
        $this->app->bind('App\Repository\ActorRepositoryInterface', 'App\Repository\Eloquent\ActorRepository');
        $this->app->bind('App\Repository\FilmRepositoryInterface', 'App\Repository\Eloquent\FilmRepository');
        $this->app->bind('App\Repository\CriticRepositoryInterface', 'App\Repository\Eloquent\CriticRepository');
        $this->app->bind('App\Repository\LanguageRepositoryInterface', 'App\Repository\Eloquent\LanguageRepository');

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
