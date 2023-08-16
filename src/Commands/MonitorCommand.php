<?php

namespace Libaro\LaravelMonitoring\Commands;

use Illuminate\Console\Command;

class MonitorCommand extends Command
{
    public $signature = 'laravel-monitoring';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
