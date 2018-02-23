<?php

namespace SquareBit\Dovetail;

use Illuminate\Support\Facades\View;
use SquareBit\Dovetail\Commands\RunTests;
use SquareBit\Dovetail\Dovetail;
use SquareBit\Dovetail\Api\Client as DovetailApiClient;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * Creates a view composer for all views.
     * Assigns the variable `route_body_classes` to each view.
     *
     * @return void
     */
    public function boot()
    {
        // Configs
        $this->publishes([
            __DIR__.'/../config/dovetail.php' => config_path('dovetail.php')
        ], 'dovetail');
        $this->mergeConfigFrom(
            __DIR__.'/../config/dovetail.php', 'dovetail'
        );

        // Create app singleton
        $this->app->singleton('dovetail', function ($httpClient, $apiKey = null, $apiUrl = null) {
            $apiClient = new \SquareBit\Dovetail\Api\Client($httpClient, $apiKey, $apiUrl);

            return $apiClient->autoloadRequest();
        });

        // Facade
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Dovetail', \SquareBit\Dovetail\Facade::class);

        // Register commands
        $this->commands([
            RunTests::class
        ]);
    }
}