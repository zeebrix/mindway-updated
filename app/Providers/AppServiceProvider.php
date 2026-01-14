<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Services\GoogleProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(GoogleProvider::class, function ($app) {
            return new GoogleProvider(
                request(),
                config('services.google.client_id'),
                config('services.google.client_secret'),
                config('services.google.redirect_uri'),
                config('services.google.scopes'),

            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
