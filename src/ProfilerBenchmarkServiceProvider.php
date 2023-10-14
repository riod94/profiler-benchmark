<?php

namespace Riod94\ProfilerBenchmark;

use Illuminate\Support\ServiceProvider;

class ProfilerBenchmarkServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('profilerbenchmark', function () {
            return new ProfilerBenchmark();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Lakukan konfigurasi atau registrasi lainnya di sini
    }
}
