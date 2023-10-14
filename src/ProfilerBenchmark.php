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
        self::setStartTime(microtime(true));
    }

    /**
     * Mengatur status ProfilerHelper.
     *
     * @param bool $enabled True jika ProfilerHelper diaktifkan, false jika tidak.
     */
    public static function enabled(bool $enabled = true)
    {
        self::setEnabled($enabled);

        return self::$enabled;
    }

    /**
     * Memulai benchmark.
     *
     * @param string $label Penanda atau penamaan benchmark.
     */
    public static function start(string $label = null)
    {
        if (! self::isEnabled()) {
            return;
        }
        self::$steps = []; // Reset langkah-langkah benchmark
        self::checkpoint($label);

        return self::$steps;
    }

    /**
     * Menyimpan langkah-langkah benchmark.
     *
     * @param string $label Penanda atau penamaan langkah benchmark.
     */
    public static function checkpoint($label = null)
    {
        if (! self::isEnabled()) {
            return;
        }

        $step = [
            'label' => $label,
            'time' => microtime(true) - self::$startTime,
            'memory' => memory_get_usage(true),
        ];

        self::addStep($step);

        return $step;
    }

    /**
     * Menghasilkan hasil benchmark.
     *
     * @return array Data benchmark.
     */
    public static function getBenchmark($label = null)
    {
        self::checkpoint($label);
        $steps = [];
        foreach (self::getSteps() as $step) {
            $stepData = [
                'label' => $step['label'],
                'time' => self::formatTime($step['time'] * 1000),
                'memory' => self::formatBytes($step['memory']),
            ];
            $steps[] = $stepData;
        }

        $benchmarkData = [
            'total_time' => self::formatTime((microtime(true) - self::$startTime) * 1000),
            'total_memory' => self::getTotalMemoryUsage(),
            'average_memory' => self::getAverageMemoryUsage(),
            'min_memory' => self::getMinMemoryUsage(),
            'max_memory' => self::getMaxMemoryUsage(),
            'steps' => $steps,
        ];

        return $benchmarkData;
    }

    /**
     * Melakukan profiling dan pengujian kinerja pada sebuah fungsi.
     *
     * @param callable $function Fungsi yang akan diprofil dan diuji kinerjanya.
     * @param int $iterations Jumlah berapa kali fungsi akan dieksekusi.
     * @param mixed ...$args Argumen yang akan diteruskan ke fungsi.
     *
     * @return array Array yang berisi data profil.
     */
    public static function functionBenchmark(callable $function, $iterations = 1, ...$args)
    {
        if (! self::$enabled) {
            return [];
        }
        $executionTimes = []; // Array untuk menyimpan waktu eksekusi setiap iterasi
        $totalTime = 0; // Total waktu eksekusi dari semua iterasi

        for ($i = 0; $i < $iterations; $i++) {
            $startTime = microtime(true); // Mendapatkan waktu saat ini dalam mikrodetik
            $return = call_user_func_array($function, $args); // Memanggil fungsi yang diberikan dengan argumen yang diberikan
            $endTime = microtime(true); // Mendapatkan waktu saat ini dalam mikrodetik
            $executionTime = ($endTime - $startTime) * 1000; // Menghitung waktu eksekusi dalam milidetik
            $executionTimes[] = $executionTime; // Menambahkan waktu eksekusi ke dalam array
            $totalTime += $executionTime; // Memperbarui total waktu eksekusi
        }

        $averageTime = array_sum($executionTimes) / count($executionTimes); // Menghitung rata-rata waktu eksekusi
        $minTime = min($executionTimes); // Mencari waktu eksekusi minimum
        $maxTime = max($executionTimes); // Mencari waktu eksekusi maksimum

        $profileData = [
            'iterations' => $iterations,
            'total_time' => self::formatTime($totalTime), // Mengformat waktu eksekusi total
            'average_time' => self::formatTime($averageTime), // Mengformat waktu eksekusi rata-rata
            'min_time' => self::formatTime($minTime), // Mengformat waktu eksekusi minimum
            'max_time' => self::formatTime($maxTime), // Mengformat waktu eksekusi maksimum
            'function' => self::$showFunction ? $function : null,
            'args' => self::$showArgs ? json_encode($args) : null,
            'return' => self::$showReturn ? json_encode($return) : null,
        ];

        return $profileData; // Mengembalikan data profil
    }
}
