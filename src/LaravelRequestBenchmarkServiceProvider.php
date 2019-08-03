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

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('request-benchmark.php'),
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'request-benchmark');
    }

}
