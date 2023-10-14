<?php

namespace Riod94\ProfilerBenchmark\Facades;

use Illuminate\Support\Facades\Facade;

class ProfilerBenchmarkFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'profilerbenchmark'; // Ganti dengan nama service yang ingin Anda gunakan
    }
}
