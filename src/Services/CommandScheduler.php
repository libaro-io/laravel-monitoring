<?php

namespace Libaro\LaravelMonitoring\Services;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Libaro\LaravelMonitoring\Commands\MonitorCommand;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;

class CommandScheduler
{
    public function __construct(
        private readonly Schedule $schedule,
    ) {
    }

    public function schedule(array $config): void
    {
        $this->scheduleDefaultCommands($config);
        $this->scheduleQueueMonitorCommands($config);
    }

    private function scheduleDefaultCommands(array $config): void
    {
        $this->schedule->command(MonitorCommand::class)->everyMinute();
    }

    private function scheduleQueueMonitorCommands(array $config): void
    {
        $queueConfig = Arr::get($config, 'queue', []);

        if (empty($queueConfig)) {
            return;
        }

        $this->schedule->command(DispatchQueueCheckJobsCommand::class)->everyMinute();
    }
}
