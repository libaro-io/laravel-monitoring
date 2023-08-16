<?php

namespace Libaro\LaravelMonitoring\Commands;

use Libaro\LaravelMonitoring\Notifications\CheckFailedNotification;

class MonitorHealthCommand extends \Spatie\Health\Commands\RunHealthChecksCommand
{
    protected $signature = 'libaro:monitor:health {--do-not-store-results} {--no-notification} {--fail-command-on-failing-check}';

    protected function getFailedNotificationClass(): string
    {
        return CheckFailedNotification::class;
    }
}
