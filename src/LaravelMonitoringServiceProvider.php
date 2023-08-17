<?php

namespace Libaro\LaravelMonitoring;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Libaro\LaravelMonitoring\Commands\MonitorCommand;
use Libaro\LaravelMonitoring\Services\CheckBuilder;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
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
        $this->mergeHealthConfig();
        $this->scheduleCommands();
    }

    public function packageRegistered()
    {
        $this->registerChecks();
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

    private function scheduleCommands(): void
    {
        /** @var Schedule $schedule */
        $schedule = app(Schedule::class);
        $schedule->command(MonitorCommand::class)->everyMinute();
        $schedule->command(DispatchQueueCheckJobsCommand::class)->everyMinute();
    }

    private function registerChecks(): void
    {
        /** @var CheckBuilder $checkBuilder */
        $checkBuilder = app(CheckBuilder::class);
        $checks = $checkBuilder->build(config('monitoring'));

        Health::checks($checks);
    }
}
