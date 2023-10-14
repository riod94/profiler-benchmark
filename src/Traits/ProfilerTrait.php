<?php

namespace Riod94\ProfilerBenchmark\Traits;

trait ProfilerTrait
{
    private static $startTime;
    private static $steps = [];
    private static $enabled = true;
    private static $showFunction = true;
    private static $showArgs = true;
    private static $showReturn = true;

    public static function setShowFunction($showFunction)
    {
        self::$showFunction = $showFunction;
    }

    public static function setShowArgs($showArgs)
    {
        self::$showArgs = $showArgs;
    }

    public static function setShowReturn($showReturn)
    {
        self::$showReturn = $showReturn;
    }

    public static function setEnabled($enabled)
    {
        self::$enabled = $enabled;
    }

    private static function isEnabled()
    {
        return self::$enabled;
    }

    private static function getStartTime()
    {
        return self::$startTime;
    }

    private static function setStartTime($startTime)
    {
        self::$startTime = $startTime;
    }

    private static function getSteps()
    {
        return self::$steps;
    }

    private static function addStep($step)
    {
        self::$steps[] = $step;
    }

    /**
     * Mengformat waktu yang diberikan dalam milidetik.
     *
     * @param int $milliseconds Waktu dalam milidetik yang akan diformat.
     * @return string Waktu yang diformat menjadi detik.
     */
    private static function formatTime($milliseconds)
    {
        // Mengformat menjadi seconds dalam desimal
        $seconds = round($milliseconds / 1000, 2);

        return $seconds;
    }

    private static function getTimeValues()
    {
        return array_column(self::$steps, 'time');
    }

    private static function getTotalMemoryUsage()
    {
        $memoryUsages = array_column(self::$steps, 'memory');

        return self::formatBytes(end($memoryUsages));
    }

    private static function getAverageMemoryUsage()
    {
        $memoryUsages = array_column(self::$steps, 'memory');

        $averageMemoryUsage = 0;
        if (0 < count($memoryUsages)) {
            $averageMemoryUsage = array_sum($memoryUsages) / count($memoryUsages);
        }

        return self::formatBytes($averageMemoryUsage);
    }

    private static function getMinMemoryUsage()
    {
        $memoryUsages = array_column(self::$steps, 'memory');

        $minMemoryUsage = 0;
        if (0 < count($memoryUsages)) {
            $minMemoryUsage = min($memoryUsages);
        }

        return self::formatBytes($minMemoryUsage);
    }

    private static function getMaxMemoryUsage()
    {
        $memoryUsages = array_column(self::$steps, 'memory');

        $maxMemoryUsage = 0;
        if (0 < count($memoryUsages)) {
            $maxMemoryUsage = max($memoryUsages);
        }

        return self::formatBytes($maxMemoryUsage);
    }

    private static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
