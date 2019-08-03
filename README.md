# Easily benchmark you're requests

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mtolhuys/laravel-request-benchmark.svg?style=flat-square)](https://packagist.org/packages/mtolhuys/laravel-request-benchmark)
[![Build Status](https://img.shields.io/travis/mtolhuys/laravel-request-benchmark/master.svg?style=flat-square)](https://travis-ci.org/mtolhuys/laravel-request-benchmark)
[![Quality Score](https://img.shields.io/scrutinizer/g/mtolhuys/laravel-request-benchmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/mtolhuys/laravel-request-benchmark)
[![Total Downloads](https://img.shields.io/packagist/dt/mtolhuys/laravel-request-benchmark.svg?style=flat-square)](https://packagist.org/packages/mtolhuys/laravel-request-benchmark)

This package comes with a benchmark middleware which, if included, will measure your requests time and memory usage.

## Installation

You can install the package via composer:

```bash
composer require mtolhuys/laravel-request-benchmark
```

## Usage

Add the `'benchmark'` middleware to the routes you want to measure f.e.: 
``` php
Route::group(['middleware' => ['foo', 'bar', 'benchmark']], function() {  
    // benchmarked routes  
});

// Or

Route::get('/', 'IndexController@index')->middleware('benchmark');
```
Out of the box this will create debug log entries f.e.:
```text
[yyyy-mm-dd hh:mm:ss] local.DEBUG: request-benchmark: GET[http://localhost]
Time: 1.05ms
Pre memory usage 3169Kb
Post memory usage 3296Kb (127Kb)  
```
This behavior is configurable with the included request-benchmark config file. 
Run `php artisan vendor:publish --provider="Mtolhuys\LaravelRequestBenchmark\LaravelRequestBenchmarkServiceProvider"` if you don't see it in your config/ folder.
```php
return [
    'enabled' => true, // set to false to globally stop benchmark
    'log' => true, // set to false to stop creating log entries
    'storage_path' => null, // if specified it will create a request-benchmark.json containing all data
];
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email mtolhuys@hotmail.com instead of using the issue tracker.

## Credits

- [Maarten Tolhuijs](https://github.com/mtolhuys)
- [All Contributors](../../contributors)
- [Laravel Package Boilerplate](https://laravelpackageboilerplate.com)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
