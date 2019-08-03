<?php

namespace Mtolhuys\LaravelRequestBenchmark;

class Benchmark
{
    /**
     * Informative label containing request method and url
     *
     * @var string $label
     */
    public static $label;

    /**
     * Float containing amount of time as microtime
     *
     * @var float $time
     */
    public static $time;

    /**
     * Array containing measured memory data
     *
     * @var array $memory
     */
    public static $memory = [
        'unit' => 'Kb',
        'pre' => null,
        'post' => null,
    ];

    /**
     * Start measuring, called before request handling
     *
     * @param string $label
     */
    public static function start(string $label)
    {
        self::$label = $label;
        self::$time = microtime(true);
        self::$memory['pre'] = self::getCurrentMemoryUsage();
    }


    /**
     * Stop measuring, called after request handling
     */
    public static function stop()
    {
        self::$memory['post'] = self::getCurrentMemoryUsage();

        self::log();
    }

    /**
     * Log and/or store measured data
     */
    private static function log(): void
    {
        $time = self::getTime();
        $memory = self::getTotalMemoryUsage();

        if (config('request-benchmark.log')) {
            \Log::debug(
                'request-benchmark: ' . self::$label . PHP_EOL
                . "Time: {$time}ms" . PHP_EOL
                . "Pre memory usage {$memory->pre}" . PHP_EOL
                . "Post memory usage {$memory->post} ({$memory->difference})"
            );
        }

        if (config('request-benchmark.storage_path')) {
            $file = config('request-benchmark.storage_path').'/request-benchmark.json';
            $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

            $data[self::$label] = [
                'time' => "{$time}ms",
                'pre_memory_usage' => $memory->pre,
                'post_memory_usage' => "{$memory->post} ({$memory->difference})",
            ];

            file_put_contents($file, json_encode($data, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
        }
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
        $diff = microtime(true) - self::$time;

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
        return round($amount / 1024);
    }
}

