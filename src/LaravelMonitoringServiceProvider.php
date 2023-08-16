<?php

namespace Libaro\LaravelMonitoring;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Libaro\LaravelMonitoring\Commands\MonitorHealthCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMonitoringServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-monitoring')
            ->hasConfigFile()
            ->hasCommands([
                MonitorHealthCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        $this->mergeHealthConfig();
    }

    private function mergeHealthConfig(): void
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            $config->set('health', array_replace_recursive(
                $config->get('health', []), $config->get('monitoring.health'),
            ));
        }
    }
}
