# Profiler Benchmark

[![License](http://poser.pugx.org/riod94/profiler-benchmark/license)](https://packagist.org/packages/riod94/profiler-benchmark)
[![Latest Stable Version](http://poser.pugx.org/riod94/profiler-benchmark/v)](https://packagist.org/packages/riod94/profiler-benchmark)
[![Total Downloads](http://poser.pugx.org/riod94/profiler-benchmark/downloads)](https://packagist.org/packages/riod94/profiler-benchmark)
[![PHP Version Require](http://poser.pugx.org/riod94/profiler-benchmark/require/php)](https://packagist.org/packages/riod94/profiler-benchmark)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/3093900130f14a219a168abb530b2a9c)](https://app.codacy.com/gh/riod94/profiler-benchmark/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

The ProfilerBenchmark library is used for benchmarking and profiling Laravel code. It is used to measure the performance of code. It is a simple and lightweight library.

## Installation

You can install this library using Composer. Run the following command in the terminal:

```bash
composer require riod94/profiler-benchmark
```

This package register the provider automatically, [See laravel package discover](https://laravel.com/docs/10.x/packages#package-discovery).

## Usage

Here is an example of how to use the ProfilerBenchmark library:

```php
use Riod94\ProfilerBenchmark\ProfilerBenchmark;

// Disable ProfilerBenchmark if your code running in production
ProfilerBenchmark::enabled(env('APP_ENV') !== 'production');

// Start benchmarking
ProfilerBenchmark::start('initialize');

// Benchmark steps
// Your Code Here
ProfilerBenchmark::checkpoint('Get start product list');

// Your Code Here
ProfilerBenchmark::checkpoint('Parse product list');
// Your Code Here

// Get benchmark results
$benchmarkData = ProfilerBenchmark::getBenchmark('Finish');

// Display benchmark results
var_dump($benchmarkData);

// Example of $benchmarkData result
array:6 [
  "total_time" => 0.02
  "total_memory" => "24.02 MB"
  "average_memory" => "15.01 MB"
  "min_memory" => "8 MB"
  "max_memory" => "24.02 MB"
  "steps" => array:4 [
    0 => array:3 [
      "label" => "initialize"
      "time" => 0.0
      "memory" => "8 MB"
    ]
    1 => array:3 [
      "label" => "Get start product list"
      "time" => 0.01
      "memory" => "12.02 MB"
    ]
    2 => array:3 [
      "label" => "Parse product list"
      "time" => 0.01
      "memory" => "16.02 MB"
    ]
    3 => array:3 [
      "label" => "Finish"
      "time" => 0.02
      "memory" => "24.02 MB"
    ]
  ]
]

// Set show function, show return and show arguments
ProfilerBenchmark::setShowFunction(true);
ProfilerBenchmark::setShowReturn(false);
ProfilerBenchmark::setShowArgs(false);

// Profile and benchmark a function
$profileData = ProfilerBenchmark::functionBenchmark(function() {
    // Code of the function to profile and benchmark
}, 9999, $args);

// Display benchmark results and profile data
var_dump($profileData);

// Example of $profileData result
array:8 [
  "iterations" => 9999
  "total_time" => 1.08
  "average_time" => 0.0
  "min_time" => 0.0
  "max_time" => 0.0
  "function" => Closure()^ {#451
    class: "Riod94\ProfilerBenchmark\Tests\ProfilerBenchmarkTest"
    this: Riod94\ProfilerBenchmark\Tests\ProfilerBenchmarkTest {#314 …}
  }
  "args" => null
  "return" => null
]

// OR you can benchmark a function of a class with arguments
$profileData1 = ProfilerBenchmark::functionBenchmark([BankAccount::class, 'getBalance'], 100, $args);

// Display benchmark results and profile data
var_dump($profileData1);

// Example of $profileData1 result
array:8 [
  "iterations" => 100
  "total_time" => 1.00
  "average_time" => 0.0
  "min_time" => 0.0
  "max_time" => 0.0
  "function" => array:2 [
    0 => "BankAccount"
    1 => "getBalance"
  ]
  "args" => null
  "return" => null
]
```

That's it! enjoy :D.

### Testing

```bash
vendor/bin/phpunit tests
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email riyo.s94@gmail.com instead of using the issue tracker.

## Credits

- [riod94](https://github.com/riod94)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
