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
     * Check if the feature is enabled.
     *
     * @return bool Returns true if the feature is enabled, false otherwise.
     */
    private static function isEnabled(): bool
    {
        return self::$enabled;
    }

    /**
     * Returns the start time.
     *
     * @return int The start time.
     */
    private static function getStartTime(): int
    {
        return self::$startTime;
    }

    /**
     * Set the start time for the function.
     *
     * @param int $startTime The start time to be set.
     * @return int The start time that was set.
     */
    private static function setStartTime(int $startTime): int
    {
        // Set the start time
        self::$startTime = $startTime;

        // Return the start time
        return self::$startTime;
    }

    /**
     * Retrieves the steps.
     *
     * @return array The steps.
     */
    private static function getSteps(): array
    {
        return self::$steps;
    }

    /**
     * Adds a step to the collection.
     *
     * @param int $time The time taken for the step.
     * @param int|float $memory The memory used for the step.
     * @param string|null $label The label for the step. If not provided, a default label will be used.
     *
     * @return array The step that was added.
     */
    private static function addStep(int $time, int|float $memory, string $label = null): array
    {
        // Create a step array with the provided time, memory, and label.
        $step = [
            'label' => $label ?? 'Step ' . count(self::$steps),
            'time' => $time,
            'memory' => $memory,
        ];

        // Add the step to the collection.
        self::$steps[] = $step;

        // Return the added step.
        return $step;
    }

    /**
     * Converts milliseconds to seconds.
     *
     * @param int $milliseconds The number of milliseconds to convert.
     * @return float The equivalent number of seconds.
     */
    private static function formatTime(int $milliseconds): float
    {
        $seconds = round($milliseconds / 1000, 2);

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
    private static function formatBytes(int|float $bytes, int $precision = 2): string
    {
        // Define the units for the byte sizes.
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        // Make sure the byte size is non-negative.
        $bytes = max($bytes, 0);

        // Calculate the power of 1024 to use for the units.
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));

        // Limit the power to the maximum unit size.
        $pow = min($pow, count($units) - 1);

        // Divide the byte size by the appropriate power of 1024.
        $bytes /= pow(1024, $pow);

        // Round the byte size to the specified precision and add the unit.
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
