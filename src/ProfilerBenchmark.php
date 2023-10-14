<?php

namespace Riod94\ProfilerBenchmark;

use Riod94\ProfilerBenchmark\Traits\ProfilerTrait;

class ProfilerBenchmark
{
    use ProfilerTrait;

    /**
     * Constructs a new instance of the class.
     */
    public function __construct()
    {
        // Set the start time to the current microtime.
        self::setStartTime(microtime(true));
    }

    /**
     * Enable or disable the feature.
     *
     * @param bool $enabled Whether the feature should be enabled or disabled.
     *
     * @return bool The new enabled status.
     */
    public static function enabled(bool $enabled = true): bool
    {
        // Set the enabled status
        self::setEnabled($enabled);

        // Return the new enabled status
        return self::$enabled;
    }

    /**
     * Start the benchmark process.
     *
     * @param string|null $label The label for the benchmark step.
     * @return array The benchmark steps.
     */
    public static function start(string|null $label = null): array
    {
        // Check if the benchmarking is enabled.
        if (!self::isEnabled()) {
            return [];
        }

        // Reset the benchmark steps.
        self::$steps = [];

        // Create a checkpoint for the current step.
        self::checkpoint($label);

        // Return the benchmark steps.
        return self::$steps;
    }

    /**
     * Logs a checkpoint with the given label.
     *
     * @param null|string $label The label for the checkpoint.
     *
     * @return array
     */
    public static function checkpoint(null|string $label = null): array
    {
        // Check if the checkpoint feature is enabled.
        if (! self::isEnabled()) {
            return [];
        }

        // Calculate the time elapsed since the start of the program.
        $time = microtime(true) - self::getStartTime();

        // Get the current memory usage.
        $memory = memory_get_usage(true);

        // Add a step to the checkpoint.
        return self::addStep($time, $memory, $label);
    }

    /**
     * Retrieves benchmark data.
     *
     * @param string|null $label The label for the benchmark data.
     * @return array The benchmark data.
     */
    public static function getBenchmark(string|null $label = null): array
    {
        // Check if the benchmarking feature is enabled
        if (!self::isEnabled()) {
            // Return an empty array if benchmarking is disabled
            return [];
        }

        // Add a checkpoint for the current label
        self::checkpoint($label);

        // Initialize an empty array to store the benchmark steps
        $steps = [];

        // Loop through each benchmark step and format the step data
        foreach (self::getSteps() as $step) {
            $stepData = [
                'label' => $step['label'],
                'time' => self::formatTime($step['time'] * 1000),
                'memory' => self::formatBytes($step['memory']),
            ];
            // Add the step data to the steps array
            $steps[] = $stepData;
        }

        // Format and store the benchmark data
        $benchmarkData = [
            'total_time' => self::formatTime((microtime(true) - self::getStartTime()) * 1000),
            'total_memory' => self::getTotalMemoryUsage(),
            'average_memory' => self::getAverageMemoryUsage(),
            'min_memory' => self::getMinMemoryUsage(),
            'max_memory' => self::getMaxMemoryUsage(),
            'steps' => $steps,
        ];

        // Return the benchmark data
        return $benchmarkData;
    }

    /**
     * Run a benchmark on a given function.
     *
     * @param callable $function The function to benchmark.
     * @param int $iterations The number of iterations to run the benchmark.
     * @param mixed ...$args The arguments to pass to the function.
     * @return array The benchmark data.
     */
    public static function functionBenchmark(callable $function, int $iterations = 1, mixed ...$args): array
    {
        if (!self::isEnabled()) {
            return [];
        }

        $executionTimes = []; // Array to store the execution time for each iteration
        $totalTime = 0; // Total execution time of all iterations

        for ($i = 0; $i < $iterations; $i++) {
            $startTime = self::setStartTime(microtime(true)); // Get the current time in microseconds
            $return = $function(...$args); // Call the given function with the given arguments using the variadic call operator
            $endTime = microtime(true); // Get the current time in microseconds
            $executionTime = ($endTime - $startTime) * 1000; // Calculate the execution time in milliseconds
            $executionTimes[] = $executionTime; // Add the execution time to the array
            $totalTime += $executionTime; // Update the total execution time
        }

        $averageTime = array_sum($executionTimes) / count($executionTimes); // Calculate the average execution time
        $minTime = min($executionTimes); // Find the minimum execution time
        $maxTime = max($executionTimes); // Find the maximum execution time

        $benchmarkData = [
            'iterations' => $iterations,
            'total_time' => self::formatTime($totalTime), // Format the total execution time
            'average_time' => self::formatTime($averageTime), // Format the average execution time
            'min_time' => self::formatTime($minTime), // Format the minimum execution time
            'max_time' => self::formatTime($maxTime), // Format the maximum execution time
            'function' => self::$showFunction ? $function : null,
            'args' => self::$showArgs ? json_encode($args) : null,
            'return' => self::$showReturn ? json_encode($return) : null,
        ];

        return $benchmarkData; // Return the benchmark data
    }
}
