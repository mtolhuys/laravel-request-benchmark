<?php

namespace Mtolhuys\LaravelRequestBenchmark;

use Illuminate\Http\Request;
use Closure;

class LaravelRequestBenchmark extends Benchmark
{
    /**
     * If enabled benchmark request handling.
     *
     * @param Request $request
     * @param Closure $next
     * @return string
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (!config('request-benchmark.enabled')) {
            return $next($request);
        }

        self::start("{$request->getMethod()}[{$request->url()}]");

        $response = $next($request);

        self::stop();

        return $response;
    }
}

