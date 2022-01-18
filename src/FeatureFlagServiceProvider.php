<?php


namespace Roboticsexpert\FeatureFlag\FeatureFlagServiceProvider;

use Illuminate\Support\ServiceProvider;
use Roboticsexpert\FeatureFlag\Services\FeatureFlagService;

class FeatureFlagServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    }

    public function register()
    {
        $this->app->singleton(FeatureFlagService::class);

    }
}
