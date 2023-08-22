<?php

namespace Libaro\LaravelMonitoring;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Libaro\LaravelMonitoring\Commands\MonitorCommand;
use Libaro\LaravelMonitoring\Services\CheckBuilder;
use Libaro\LaravelMonitoring\Services\CommandScheduler;
use Spatie\Health\Facades\Health;
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
                MonitorCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        $this->app->booted(fn () => $this->appBooted());
    }

    public function appBooted(): void
    {
        $this->mergeHealthConfig();
        $this->scheduleCommands();
    }

    public function packageRegistered(): void
    {
        $this->registerChecks();
    }

    private function mergeHealthConfig(): void
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            /** @var Repository $config */
            $config = $this->app->make('config');

            $healthConfig = $config->get('health', []);

            unset($healthConfig['result_stores']);

            $config->set('health', array_replace_recursive(
                $healthConfig, $config->get('monitoring.health', []),
            ));
        }
    }

    private function scheduleCommands(): void
    {
        /** @var CommandScheduler $scheduler */
        $scheduler = app(CommandScheduler::class);
        $scheduler->schedule(config('monitoring'));
    }

    private function registerChecks(): void
    {
        /** @var CheckBuilder $checkBuilder */
        $checkBuilder = app(CheckBuilder::class);
        $checks = $checkBuilder->build(config('monitoring'));

        Health::checks($checks);
    }
}
