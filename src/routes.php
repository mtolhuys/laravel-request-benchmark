<?php

use Mtolhuys\LaravelRequestBenchmark\Controllers\BenchmarkResultsController;

Route::get('/request-benchmark', [BenchmarkResultsController::class, 'index']);
