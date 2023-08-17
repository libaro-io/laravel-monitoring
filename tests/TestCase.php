<?php

namespace Libaro\LaravelMonitoring\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Libaro\LaravelMonitoring\LaravelMonitoringServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Health\HealthServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Libaro\\LaravelMonitoring\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            HealthServiceProvider::class,
            LaravelMonitoringServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-monitoring_table.php.stub';
        $migration->up();
        */
    }
}
