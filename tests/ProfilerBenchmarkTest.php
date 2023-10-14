<?php

namespace Riod94\ProfilerBenchmark\Tests;

use PHPUnit\Framework\TestCase;
use Riod94\ProfilerBenchmark\ProfilerBenchmark;

class ProfilerBenchmarkTest extends TestCase
{
    /**
     * Test if the ProfilerBenchmark is enabled.
     *
     * @return void
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
     * Test the `checkpoint` method of the ProfilerBenchmark class.
     *
     * @return void
     */
    public function testCheckpoint()
    {
        $this->assertIsArray(ProfilerBenchmark::checkpoint());
    }

    /**
     * Test the result of the ProfilerBenchmark::getResult() function.
     *
     * @return void
     */
    public function testGetBenchmark()
    {
        $this->assertIsArray(ProfilerBenchmark::getBenchmark());
    }

    /**
     * Test the setShowFunction method of the ProfilerBenchmark class.
     *
     * @return void
     */
    public function testSetShowFunction()
    {
        $this->assertTrue(ProfilerBenchmark::setShowFunction(true));
    }

    /**
     * This function tests the method `setShowArgs` of the `ProfilerBenchmark` class.
     *
     * @return void
     */
    public function testSetShowArgs()
    {
        $this->assertFalse(ProfilerBenchmark::setShowArgs(false));
    }

    /**
     * A description of the entire PHP function.
     *
     * @return void
     */
    public function testSetShowReturn()
    {
        $this->assertFalse(ProfilerBenchmark::setShowReturn(false));
    }

    /**
     * Test the profile and benchmark functionality.
     *
     * @return void
     */
    public function testFunctionBenchmark()
    {
        $functionBenchmark = ProfilerBenchmark::functionBenchmark(function () {
            $nums = [];
            for ($i = 0; $i < 9999; $i++) {
                $nums[] = $i;
            }
            return $nums;
        }, 1);

        $this->assertIsArray($functionBenchmark);
    }

    /**
     * Test the profile and benchmark functionality.
     *
     * @return void
     */
    public function testFunctionBenchmark2()
    {
        $functionBenchmark = ProfilerBenchmark::functionBenchmark([ProfilerBenchmark::class, 'getBenchmark'], 1, 'Test Benchmark');

        $this->assertIsArray($functionBenchmark);
    }

    /**
     * Test the complexity of the function.
     *
     * @return void
     */
    public function testComplexity()
    {
        $benchmark = new ProfilerBenchmark();
        $x = 0;
        $collect = [];
        $benchmark->start('initialize');
        for ($i = 0; $i < 99999; $i++) {
            $x += $i;
            $collect[] = $x;
        }
        // code here
        $benchmark->checkpoint('Get start product list');
        for ($i = 0; $i < 99999; $i++) {
            $x += $i;
            $collect[] = $x;
        }
        // code here
        $benchmark->checkpoint('Parse product list');
        // code here
        for ($i = 0; $i < 99999; $i++) {
            $x += $i;
            $collect[] = $x;
        }

        $result = $benchmark->getBenchmark('Finish');
        $this->assertIsArray($result);
    }
}
