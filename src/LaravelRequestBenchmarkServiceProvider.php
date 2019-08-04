<?php

namespace Mtolhuys\LaravelRequestBenchmark;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaravelRequestBenchmarkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @param Router $router
     */
    public function boot(Router $router)
    {
        $router->aliasMiddleware('benchmark', LaravelRequestBenchmark::class);
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'request-benchmark');

        $this->publishes([
            __DIR__ . '/config' => config_path('request-benchmark.php'),
        ]);

        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/request-benchmark'),
        ], 'views');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/request-benchmark.php', 'request-benchmark');
    }

}
