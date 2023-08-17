# Monitoring and alerting for Laravel applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/libaro-io/laravel-monitoring.svg?style=flat-square)](https://packagist.org/packages/libaro-io/laravel-monitoring)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/libaro-io/laravel-monitoring/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/libaro-io/laravel-monitoring/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/libaro-io/laravel-monitoring/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/libaro-io/laravel-monitoring/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/libaro-io/laravel-monitoring.svg?style=flat-square)](https://packagist.org/packages/libaro-io/laravel-monitoring)

The Libaro monitoring package, a config based monitoring setup for your Laravel applications.

## Installation

You can install the package using composer:

```bash
composer require libaro-io/laravel-monitoring
```

## Configuration

### Publishing the configuraiton

You can publish [the config file](./config/monitoring.php) with:

```bash
php artisan vendor:publish --tag="monitoring-config"
```

### Queue monitoring

You can enable queue monitoring by specifying your queue name as an array key followed by the configuration for that queue.

| Option           |  Value  | Description                                                                                                                        |
|------------------|:-------:|------------------------------------------------------------------------------------------------------------------------------------|
| max_wait_minutes |   int   | A test job will be dispatched on that queue. When the job is not processed within the specified time, a notification will be sent. |

```php
[
    ...

    'queue' => [
        'invoicing' => [ // queue name 
            'max_wait_minutes' => 5, // Fails when test job is not processed within 5 minutes
        ],
    ],
    
    ...
]
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Kim Bouchouaram](https://github.com/neemspees)
- [All Contributors](../../contributors)
- [Spatie](https://spatie.be/) (Package Skeleton, Laravel Health)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
