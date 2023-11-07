<?php

namespace Riod94\ProfilerBenchmark;

use ReflectionClass;
use Riod94\ProfilerBenchmark\Traits\ProfilerTrait;

class ProfilerBenchmark
{
    use ProfilerTrait;

    /**
     * Constructor for the class.
     * Sets the start time of the object.
     */
    public function __construct()
    {
        // Set the start time using the current microtime
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
    public static function start(string $label = null): array
    {
        // Check if the benchmarking is enabled.
        if (! self::$enabled) {
            return [];
        }

        self::setStartTime(microtime(true));

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
    public static function checkpoint(string $label = null): array
    {
        // Check if the checkpoint feature is enabled.
        if (! self::$enabled) {
            return [];
        }

        // Add a step to the checkpoint.
        return self::addStep($label);
    }

    /**
     * Retrieves benchmark data.
     *
     * @param string|null $label The label for the benchmark data.
     * @return array The benchmark data.
     */
    public static function getBenchmark(string $label = null): array
    {
        // Check if the benchmarking feature is enabled
        if (! self::$enabled) {
            // Return an empty array if benchmarking is disabled
            return [];
        }

        // Add a checkpoint for the current label
        self::checkpoint($label);

        // Initialize an empty array to store the benchmark steps
        $steps = [];

        // Loop through each benchmark step and format the step data
        foreach (self::$steps as $step) {
            $stepData = [
                'label' => $step['label'],
                'time' => self::formatTime($step['time']),
                'memory' => self::formatBytes($step['memory']),
            ];
            // Add the step data to the steps array
            $steps[] = $stepData;
        }

        // Format and store the benchmark data
        $benchmarkData = [
            'total_time' => self::formatTime(microtime(true) - self::$startTime),
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
     * @param callable|array $function The function to benchmark.
     * @param int $iterations The number of iterations to run the benchmark.
     * @param mixed ...$args The arguments to pass to the function.
     * @return array The benchmark data.
     */
    public static function functionBenchmark($function, int $iterations = 1, mixed ...$args): array
    {
        // If benchmarking is disabled, return an empty array
        if (! self::$enabled) {
            return [];
        }

        // Start the function benchmark iteration
        self::checkpoint('Start Function Benchmark Iteration');

        $executionTimes = []; // Array to store the execution time for each iteration
        $totalTime = 0; // Total execution time of all iterations

        $className = null;
        $methodName = null;

        // If the function is an array with two elements, assign the class name and method name
        if (is_array($function) && count($function) === 2) {
            $className = $function[0];
            $methodName = $function[1];
        }

        for ($i = 0; $i < $iterations; $i++) {
            $startTime = self::setStartTime(microtime(true)); // Get the current time in microseconds

            // Call the function using reflection method instead of direct invocation
            $return = self::callReflectionMethod($className, $methodName, $args);

            $endTime = microtime(true); // Get the current time in microseconds
            $executionTime = ($endTime - $startTime); // Calculate the execution time
            $executionTimes[] = $executionTime; // Add the execution time to the array
            $totalTime += $executionTime; // Update the total execution time
        }

        // Finish the function benchmark iteration
        self::checkpoint('Finish Function Benchmark Iteration');

        $averageTime = array_sum($executionTimes) / count($executionTimes); // Calculate the average execution time
        $minTime = min($executionTimes); // Find the minimum execution time
        $maxTime = max($executionTimes); // Find the maximum execution time

        $benchmarkData = [
            'function' => self::$showFunction ? $function : null, // Include the function in the benchmark data if showFunction is enabled
            'args' => self::$showArgs ? json_encode($args) : null, // Include the arguments in the benchmark data if showArgs is enabled
            'return' => self::$showReturn ? json_encode($return) : null, // Include the return value in the benchmark data if showReturn is enabled
            'steps' => self::$showSteps ? json_encode(self::$steps) : null, // Include the steps in the benchmark data
            'iterations' => $iterations,
            'total_time' => self::formatTime($totalTime), // Format the total execution time
            'average_time' => self::formatTime($averageTime), // Format the average execution time
            'min_time' => self::formatTime($minTime), // Format the minimum execution time
            'max_time' => self::formatTime($maxTime), // Format the maximum execution time
            'total_memory' => self::getTotalMemoryUsage(), // Get the total memory usage
            'average_memory' => self::getAverageMemoryUsage(), // Get the average memory usage
            'min_memory' => self::getMinMemoryUsage(), // Get the minimum memory usage
            'max_memory' => self::getMaxMemoryUsage(), // Get the maximum memory usage
        ];

        // Return the benchmark data
        return $benchmarkData;
    }

    /**
     * Calls a method using reflection.
     *
     * @param string $className The name of the class.
     * @param string $methodName The name of the method.
     * @param array $args The arguments to pass to the method.
     * @return mixed|null The result of the method call or null if the method does not exist.
     */
    private static function callReflectionMethod($className, $methodName, $args)
    {
        // Check if the class name and method name are provided
        if ($className && $methodName) {
            $reflectionClass = new ReflectionClass($className);

            // Check if the method exists in the class
            if ($reflectionClass->hasMethod($methodName)) {
                $reflectionMethod = $reflectionClass->getMethod($methodName);
                $reflectionMethod->setAccessible(true); // Allow access to protected method

                $instance = null;
                // Create an instance of the class if the method is not static
                if (!$reflectionMethod->isStatic()) {
                    $instance = $reflectionClass->newInstanceWithoutConstructor();
                }

                // Call the method with the provided arguments
                return $reflectionMethod->invokeArgs($instance, $args);
            }
        }

        return null;
    }

}
