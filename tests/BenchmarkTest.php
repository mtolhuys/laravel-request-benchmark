<?php

namespace Mtolhuys\LaravelRequestBenchmark\Tests;

use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase;
use Mtolhuys\LaravelRequestBenchmark\LaravelRequestBenchmark;
use Illuminate\Http\Request;

class BenchmarkTest extends TestCase
{
    /** @test */
    public function benchmark_results_are_logged_and_written_to_json_file()
    {
        $this->app['config']->set('request-benchmark.log', true);
        $this->app['config']->set('request-benchmark.enabled', true);
        $this->app['config']->set('request-benchmark.storage_path', storage_path());

        Log::shouldReceive('debug')->once();

        $request = Request::create('/');

        $middleware = new LaravelRequestBenchmark();

        $middleware->handle($request, static function () {});

        $content = file_get_contents(storage_path('request-benchmark.json'));

        $this->assertContains($request->url(), $content);
        $this->assertContains('"time":', $content);
        $this->assertContains('"pre_memory_usage":', $content);
        $this->assertContains('"post_memory_usage":', $content);
    }
}
