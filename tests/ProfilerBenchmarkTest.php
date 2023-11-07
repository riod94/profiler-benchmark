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
        $profiler = new ProfilerBenchmark();
        $this->assertTrue($profiler->enabled());
    }

    /**
     * Test the start function of the ProfilerBenchmark class.
     *
     * @return void
     */
    public function testStart()
    {
        $profiler = new ProfilerBenchmark();
        $this->assertIsArray($profiler->start('Initialize'));
    }

    /**
     * Test the `checkpoint` method of the ProfilerBenchmark class.
     *
     * @return void
     */
    public function testCheckpoint()
    {
        $profiler = new ProfilerBenchmark();
        $this->assertIsArray($profiler->checkpoint('Get start product list'));
    }

    /**
     * Test the result of the ProfilerBenchmark::getResult() function.
     *
     * @return void
     */
    public function testGetBenchmark()
    {
        $profiler = new ProfilerBenchmark();
        $this->assertIsArray($profiler->getBenchmark('Finish'));
    }

    /**
     * Test the setShowFunction method of the ProfilerBenchmark class.
     *
     * @return void
     */
    public function testSetShowFunction()
    {
        $profiler = new ProfilerBenchmark();
        $this->assertTrue($profiler->setShowFunction(true));
    }

    /**
     * This function tests the method `setShowArgs` of the `ProfilerBenchmark` class.
     *
     * @return void
     */
    public function testSetShowArgs()
    {
        $profiler = new ProfilerBenchmark();
        $this->assertFalse($profiler->setShowArgs(false));
    }

    /**
     * A description of the entire PHP function.
     *
     * @return void
     */
    public function testSetShowReturn()
    {
        $profiler = new ProfilerBenchmark();
        $this->assertFalse($profiler->setShowReturn(false));
    }

    /**
     * Test the profile and benchmark functionality.
     *
     * @return void
     */
    public function testFunctionBenchmark()
    {
        $profiler = new ProfilerBenchmark();
        $functionBenchmark = $profiler->functionBenchmark(function () {
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
        $profiler = new ProfilerBenchmark();
        $functionBenchmark = $profiler->functionBenchmark([ProfilerBenchmark::class, 'getBenchmark'], 1);

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
