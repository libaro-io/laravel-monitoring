<?php

namespace Libaro\LaravelMonitoring\Commands;

use Libaro\LaravelMonitoring\Notifications\CheckFailedNotification;

class MonitorCommand extends \Spatie\Health\Commands\RunHealthChecksCommand
{
    protected $signature = 'libaro:monitor {--do-not-store-results} {--no-notification} {--fail-command-on-failing-check}';

    public function handle(): int
    {
        return parent::handle();
    }

    protected function getFailedNotificationClass(): string
    {
        return CheckFailedNotification::class;
    }
}
