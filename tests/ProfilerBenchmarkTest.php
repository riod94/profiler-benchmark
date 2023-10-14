<?php

namespace Riod94\ProfilerBenchmark\Tests;

use PHPUnit\Framework\TestCase;
use Riod94\ProfilerBenchmark\ProfilerBenchmark;

class ProfilerBenchmarkTest extends TestCase
{
    /**
     * Test if the profiler is enabled.
     *
     * @throws Exception if the profiler is not enabled.
     */
    public function testEnabled()
    {
        $this->assertTrue(ProfilerBenchmark::enabled());
    }

    /**
     * Test the start function of the ProfilerBenchmark class.
     *
     * @return void
     */
    public function testStart()
    {
        $this->assertIsArray(ProfilerBenchmark::start());
    }

    /**
     * Test the 'checkpoint' function.
     *
     * @throws AssertionFailedError If the returned value is not an array.
     * @return void
     */
    public function testCheckpoint()
    {
        $this->assertIsArray(ProfilerBenchmark::checkpoint());
    }

    /**
     * Test the result of the ProfilerBenchmark::getResult() function.
     *
     * @throws Some_Exception_Class description of exception
     */
    public function testGetBenchmark()
    {
        $this->assertIsArray(ProfilerBenchmark::getBenchmark());
    }

    /**
     * Test the profile and benchmark functionality.
     *
     * @return void
     */
    public function testFunctionBenchmark()
    {
        ProfilerBenchmark::setShowReturn(false);
        ProfilerBenchmark::setShowArgs(false);
        $functionBenchmark = ProfilerBenchmark::functionBenchmark([ProfilerBenchmark::class, 'getBenchmark'], 10);

        $functionBenchmark = ProfilerBenchmark::functionBenchmark(function () {
            $nums = [];
            for ($i = 0; $i < 9999; $i++) {
                $nums[] = $i;
            }
            return $nums;
        }, 9999);

        $this->assertIsArray($functionBenchmark);
    }

    public function testComplexity()
    {
        $benchmark = new ProfilerBenchmark();
        $x = 0;
        $collect = collect();
        $benchmark->start('initialize');
        for ($i = 0; $i < 99999; $i++) {
            $x += $i;
            $collect->push($x);
        }
        // code here
        $benchmark->checkpoint('Get start product list');
        for ($i = 0; $i < 99999; $i++) {
            $x += $i;
            $collect->push($x);
        }
        // code here
        $benchmark->checkpoint('Parse product list');
        // code here
        for ($i = 0; $i < 99999; $i++) {
            $x += $i;
            $collect->push($x);
        }

        $result = $benchmark->getBenchmark('Finish');
        $this->assertIsArray($result);
    }
}
