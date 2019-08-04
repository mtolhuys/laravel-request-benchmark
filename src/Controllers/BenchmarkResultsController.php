<?php

namespace Mtolhuys\LaravelRequestBenchmark\Controllers;

class BenchmarkResultsController
{
    private $bestRequests = [];

    private $worstRequests = [];

    public function index()
    {
        $file = config('request-benchmark.storage_path') . '/request-benchmark.json';
        $requests = [];
        $history = [];

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);

            if (isset($data['history'])) {
                $history = $data['history'];

                unset($data['history']);
            }

            $requests = $data;

            $this->compareRequests($history);
        }

        return view('request-benchmark::results', [
            'requests' => $requests,
            'history' => $history,
            'bestRequests' => $this->bestRequests,
            'worstRequests' => $this->worstRequests,
        ]);
    }

    private function compareRequests(array $history): array
    {
        $bestRequests = [];

        foreach ($history as $request => $requestHistory) {
            if (count($requestHistory) <= 1) {
                continue;
            }

            foreach ($requestHistory as $timestamp => $result) {
                $this->compare($request, $timestamp, $result);
            }

        }

        return $bestRequests;
    }

    private function compare(string $request, string $timestamp, array $result)
    {
        if ($this->firstInHistory($request)) {
            $this->storeBestRequest($request, $timestamp, $result);
            $this->storeWorstRequest($request, $timestamp, $result);

            return;
        }

        $time = $this->getTime($result['time']);
        $memoryUsage = $this->getMemoryUsage($result['actual_memory_usage']);

        if ($this->isBest($request, $time, $memoryUsage)) {
            $this->storeBestRequest($request, $timestamp, $result);
        }

        if ($this->isWorst($request, $time, $memoryUsage)) {
            $this->storeWorstRequest($request, $timestamp, $result);
        }
    }

    private function firstInHistory(string $request): bool
    {
        return (empty($this->bestRequests) && empty($this->worstRequests))
            || (! isset($this->bestRequests[$request]) && ! isset($this->worstRequests[$request]));
    }

    private function isBest(string $request, float $time, float $memoryUsage): bool
    {
        $bestTime = $this->getTime($this->bestRequests[$request]->result->time);
        $bestMemoryUsage = $this->getMemoryUsage(
            $this->bestRequests[$request]->result->actual_memory_usage
        );

        if ($memoryUsage <= $bestMemoryUsage) {
            $difference = $bestMemoryUsage - $memoryUsage;

            return $difference > 1000 || $time < $bestTime;
        }

        return false;
    }

    private function isWorst(string $request, float $time, float $memoryUsage): bool
    {
        $worstTime = $this->getTime($this->worstRequests[$request]->result->time);
        $worstMemoryUsage = $this->getMemoryUsage(
            $this->worstRequests[$request]->result->actual_memory_usage
        );

        if ($memoryUsage >= $worstMemoryUsage) {
            $difference = $memoryUsage - $worstMemoryUsage;

            return $difference > 1000 || $worstTime < $time;
        }

        return false;
    }

    private function getTime(string $time): float
    {
        return (float) filter_var(
            $time,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );
    }

    private function getMemoryUsage(string $actualMemoryUsage): float
    {
        $memoryUsage = filter_var(
            $actualMemoryUsage,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );

        if (strpos($actualMemoryUsage, 'Mb') !== false) {
            return (float) $memoryUsage * 1024;
        }

        return (float) $memoryUsage;
    }

    private function storeBestRequest(string $request, string $timestamp, array $result)
    {
        $this->bestRequests[$request] = (object) [
            'timestamp' => $timestamp,
            'result' => (object) $result,
        ];
    }

    private function storeWorstRequest(string $request, string $timestamp, array $result)
    {
        $this->worstRequests[$request] = (object) [
            'timestamp' => $timestamp,
            'result' => (object) $result,
        ];
    }
}
