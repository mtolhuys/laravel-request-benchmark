<?php

namespace Mtolhuys\LaravelRequestBenchmark;

class Benchmark
{
    /**
     * Informative label containing request method and url
     *
     * @var string $request
     */
    private static $request;

    /**
     * Float containing amount of time as microtime
     *
     * @var float $runtime
     */
    private static $runtime;

    /**
     * Array containing measured memory data
     *
     * @var array $memory
     */
    private static $memory = [
        'unit' => 'Kb',
        'pre' => null,
        'post' => null,
    ];

    /**
     * Start measuring, called before request handling
     *
     * @param string $request
     */
    public static function start(string $request)
    {
        self::$request = $request;
        self::$runtime = microtime(true);
        self::$memory['pre'] = self::getCurrentMemoryUsage();
    }


    /**
     * Stop measuring, called after request handling
     *
     * @throws \Exception
     */
    public static function stop()
    {
        self::$memory['post'] = self::getCurrentMemoryUsage();

        self::log();
    }

    /**
     * Log and/or store measured data
     *
     * @throws \Exception
     */
    private static function log()
    {
        $result = self::getResult();

        if (config('request-benchmark.log')) {
            \Log::debug(
                'request-benchmark: ' . self::$request . PHP_EOL
                . "Time: {$result->time}ms" . PHP_EOL
                . "Pre memory usage {$result->pre_memory_usage}" . PHP_EOL
                . "Post memory usage {$result->post_memory_usage} ({$result->actual_memory_usage})"
            );
        }

        if (config('request-benchmark.storage_path')) {
            self::storeJson($result);
        }
    }


    private static function getResult()
    {
        $time = self::getTime();
        $memory = self::getTotalMemoryUsage();

        return (object)[
            'time' => "{$time}ms",
            'pre_memory_usage' => $memory->pre,
            'post_memory_usage' => $memory->post,
            'actual_memory_usage' => $memory->difference,
        ];
    }

    /**
     * Get current memory usage for measuring
     *
     * @return float
     */
    private static function getCurrentMemoryUsage(): float
    {
        $usage = self::increaseMagnitude(memory_get_usage());

        if ($usage >= (10 * 1000)) {
            self::$memory['unit'] = 'Mb';
        }

        return $usage;
    }

    /**
     * Get total measured memory usage
     *
     * @return object
     */
    private static function getTotalMemoryUsage()
    {
        $unit = self::$memory['unit'];
        $pre = self::$memory['pre'];
        $post = self::$memory['post'];

        if (self::$memory['unit'] === 'Mb') {
            $pre = self::increaseMagnitude($pre);
            $post = self::increaseMagnitude($post);
        }

        return (object) [
            'pre' => "{$pre}{$unit}",
            'post' => "{$post}{$unit}",
            'difference' => ($post - $pre) . $unit,
        ];
    }

    /**
     * Get time between now and start() invocation
     *
     * @return float
     */
    private static function getTime(): float
    {
        $diff = microtime(true) - self::$runtime;

        return round(($diff - (int)$diff) * 1000, 2);
    }

    /**
     * Increasing measured byte $amount in magnitude Kb -> Mb etc.
     *
     * @param int $amount
     * @return float
     */
    private static function increaseMagnitude(int $amount): float
    {
        return round($amount / 1024, 2);
    }

    /**
     * @param $result
     *
     * @throws \Exception
     */
    private static function storeJson($result)
    {
        $file = config('request-benchmark.storage_path').'/request-benchmark.json';
        $requests = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

        if (isset($requests[self::$request])) {
            $requests['history'] = self::history($requests);
        }

        $requests[self::$request] = $result;

        file_put_contents($file, json_encode($requests, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
    }

    /**
     * @param array $requests
     * @return mixed
     * @throws \Exception
     */
    private static function history(array $requests)
    {
        $timestamp = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $history = $requests['history'] ?? [];

        if (!isset($history[self::$request]) || !self::maxHistoryReached($history)) {
            $history[self::$request][$timestamp] = $requests[self::$request];
        }

        else {
            unset($history[self::$request][array_key_last($history[self::$request])]);

            $history[self::$request] = [
                $timestamp => $requests[self::$request]
            ] + $history[self::$request];
        }

        return $history;
    }

    /**
     * @param array $history
     * @return bool
     */
    private static function maxHistoryReached(array $history): bool
    {
        return config('request-benchmark.max_history') <= count($history[self::$request]);
    }
}

