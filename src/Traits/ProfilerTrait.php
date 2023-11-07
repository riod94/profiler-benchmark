<?php

namespace Riod94\ProfilerBenchmark\Traits;

trait ProfilerTrait
{
    private static $startTime = 0;
    private static $steps = [];
    private static $enabled = true;
    private static $showFunction = true;
    private static $showArgs = true;
    private static $showReturn = true;
    private static $showSteps = true;

    /**
     * Set the value to determine whether the function should be shown.
     *
     * @param bool $showFunction Set to true to show the function, false otherwise.
     * @return void
     */
    public static function setShowFunction(bool $showFunction): bool
    {
        // Update the class property with the new value.
        self::$showFunction = $showFunction;

        return self::$showFunction;
    }

    /**
     * Set whether to show arguments when displaying information.
     *
     * @param bool $showArgs Whether to show arguments or not. Default is true.
     * @return void
     */
    public static function setShowArgs(bool $showArgs): bool
    {
        self::$showArgs = $showArgs;

        return self::$showArgs;
    }

    /**
     * Sets the value of the $showReturn property.
     *
     * @param bool $showReturn The new value for the $showReturn property.
     */
    public static function setShowReturn(bool $showReturn): bool
    {
        self::$showReturn = $showReturn;

        return self::$showReturn;
    }

    /**
     * Sets the value of the $showSteps property.
     *
     * @param bool $showSteps The new value for the $showSteps property.
     */
    public static function setShowSteps(bool $showSteps): bool
    {
        self::$showSteps = $showSteps;

        return self::$showSteps;
    }

    /**
     * Set the enabled state.
     *
     * @param bool $enabled The new enabled state.
     */
    public static function setEnabled(bool $enabled): bool
    {
        self::$enabled = $enabled;

        return self::$enabled;
    }

    /**
     * Set the start time for the function.
     *
     * @param float $startTime The start time to be set.
     * @return float The start time that was set.
     */
    private static function setStartTime(float $startTime): float
    {
        self::$startTime = $startTime;

        return self::$startTime;
    }

    /**
     * Adds a step to the array of steps.
     *
     * @param string|null $label The label for the step. If null, a default label will be used.
     *
     * @return array The added step.
     */
    private static function addStep(string $label = null): array
    {
        // Get the memory limit and parse it into bytes
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = self::parseMemoryLimit($memoryLimit);

        // Get the average memory usage and parse it into bytes
        $averageMemoryUsage = self::getAverageMemoryUsage();
        $averageMemoryUsageBytes = self::parseMemoryLimit($averageMemoryUsage);

        // Get the current memory usage
        $memory = memory_get_usage(true);

        // Check if there is enough memory available
        if (($memoryLimitBytes - $memory) > $averageMemoryUsageBytes) {
            // Free up memory if necessary
            self::freeUpMemory($memory, $memoryLimitBytes);
        }

        // Create the step array
        $step = [
            'label' => $label ?? 'Step ' . count(self::$steps),
            'time' => (microtime(true) - self::$startTime),
            'memory' => memory_get_usage(),
        ];

        // Add the step to the array of steps
        self::$steps[] = $step;

        // Return the added step
        return $step;
    }

    /**
     * Converts milliseconds to seconds.
     *
     * @param float $milliseconds The number of milliseconds to convert.
     * @return float The equivalent number of seconds.
     */
    private static function formatTime(float $milliseconds): float
    {
        $seconds = round($milliseconds, 2);

        return $seconds;
    }

    /**
     * Returns the total memory usage of all steps.
     *
     * @return string The formatted memory usage in bytes.
     */
    private static function getTotalMemoryUsage(): string
    {
        // Get the memory usages from all steps
        $memoryUsages = array_column(self::$steps, 'memory');

        // Return the formatted memory usage of the latest step
        return self::formatBytes(end($memoryUsages));
    }

    /**
     * Returns the average memory usage of the steps.
     *
     * @return string The formatted average memory usage.
     */
    private static function getAverageMemoryUsage(): string
    {
        // Get an array of memory usages from the steps
        $memoryUsages = array_column(self::$steps, 'memory');

        // Calculate the average memory usage
        $averageMemoryUsage = 0;
        if (0 < count($memoryUsages)) {
            $averageMemoryUsage = array_sum($memoryUsages) / count($memoryUsages);
        }

        // Format the average memory usage and return it
        return self::formatBytes($averageMemoryUsage);
    }

    /**
     * Get the minimum memory usage from the steps array.
     *
     * @return string The formatted minimum memory usage.
     */
    private static function getMinMemoryUsage(): string
    {
        // Get the memory usages from the steps array
        $memoryUsages = array_column(self::$steps, 'memory');

        // Set the initial minimum memory usage to 0
        $minMemoryUsage = 0;

        // If there are memory usages
        if (0 < count($memoryUsages)) {
            // Get the minimum memory usage from the array
            $minMemoryUsage = min($memoryUsages);
        }

        // Format the minimum memory usage in bytes and return it
        return self::formatBytes($minMemoryUsage);
    }

    /**
     * Get the maximum memory usage among the steps.
     *
     * @return string The formatted maximum memory usage.
     */
    private static function getMaxMemoryUsage(): string
    {
        // Get the memory usages of all steps
        $memoryUsages = array_column(self::$steps, 'memory');

        // Find the maximum memory usage
        $maxMemoryUsage = 0;
        if (count($memoryUsages) > 0) {
            $maxMemoryUsage = max($memoryUsages);
        }

        // Format the maximum memory usage and return it
        return self::formatBytes($maxMemoryUsage);
    }

    /**
     * Format the given number of bytes into a human-readable string.
     *
     * @param int|float $bytes The number of bytes to format.
     * @param int $precision The number of decimal places to round to (default is 2).
     * @return string The formatted string.
     */
    private static function formatBytes($bytes, int $precision = 2): string
    {
        // Define the units for the byte sizes.
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        // Get the total number of units.
        $totalUnits = count($units);

        // Loop through the units until the bytes value is less than or equal to 1024.
        for ($i = 0; $i < $totalUnits && $bytes > 1024; $i++) {
            $bytes /= 2 ** 10;
        }

        // Round the bytes value to the specified precision and concatenate it with the unit.
        return round($bytes, $precision) . $units[$i];
    }

    /**
     * Parses the memory limit value and converts it to bytes.
     *
     * @param string $memoryLimit The memory limit value to parse.
     * @return int The memory limit value in bytes.
     */
    private static function parseMemoryLimit(string $memoryLimit): int
    {
        // Convert the memory limit value to an integer
        $value = (int) $memoryLimit;

        // Get the unit of the memory limit value (last character)
        $unit = strtolower(substr($memoryLimit, -1));

        // Convert the memory limit value to bytes based on the unit
        switch ($unit) {
            case 'g':
                $value *= 1024;
                // fallthrough
                // no break
            case 'm':
                $value *= 1024;
                // fallthrough
                // no break
            case 'k':
                $value *= 1024;
                break;
        }

        // Return the memory limit value in bytes
        return $value;
    }

    /**
     * Frees up memory by removing old steps from the array.
     *
     * @param int $estimatedMemoryUsage The estimated memory usage.
     * @param int $memoryLimit The memory limit.
     * @return void
     */
    private static function freeUpMemory(int $estimatedMemoryUsage, int $memoryLimit): void
    {
        // Remove old steps from the array until the estimated memory usage is below the memory limit
        while ($estimatedMemoryUsage >= $memoryLimit && count(self::$steps) > 0) {
            // Get the oldest step and remove it from the array
            $oldestStep = array_shift(self::$steps);

            // Subtract the memory usage of the oldest step from the estimated memory usage
            $estimatedMemoryUsage -= $oldestStep['memory'];
        }
    }
}
