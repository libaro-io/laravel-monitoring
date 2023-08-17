<?php

namespace Libaro\LaravelMonitoring\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Checks\QueueCheck;

class CheckBuilder
{
    /**
     * @return Check[]
     */
    public function build(array $config): array
    {
        return $this->buildQueueChecks($config)->all();
    }

    private function buildQueueChecks(array $config): Collection
    {
        $queueConfig = Arr::get($config, 'queue', []);

        return collect($queueConfig)
            ->map(function (array $queueConfig, string $queue) {
                return QueueCheck::new()
                    ->name(sprintf('Queue "%s"', $queue))
                    ->onQueue($queue)
                    ->failWhenHealthJobTakesLongerThanMinutes(Arr::get($queueConfig, 'max_wait_minutes', 5));
            });
    }
}
