<?php

namespace Mtolhuys\LaravelRequestBenchmark;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mtolhuys\LaravelRequestBenchmark\Skeleton\SkeletonClass
 */
class LaravelRequestBenchmarkFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-request-benchmark';
    }
}
