<?php

use Illuminate\Console\Application;
use Illuminate\Console\Scheduling\Schedule;
use Libaro\LaravelMonitoring\Services\CommandScheduler;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;

function assertCommandScheduled(Schedule $schedule, string $command, string $expression): void
{
    $itemsWithCommand = collect($schedule->events())
        ->where('command', Application::formatCommandString($command));

    expect($itemsWithCommand)
        ->not->toBeEmpty(sprintf('Failed asserting that the "%s" command was scheduled', $command));

    $itemsWithCommandAndExpression = $itemsWithCommand
        ->where('expression', $expression);

    expect($itemsWithCommandAndExpression)
        ->not->toBeEmpty(sprintf('Failed asserting that the "%s" command was scheduled with expression "%s"', $command, $expression));
}

it('schedules correct default commands', function (string $command, string $expression) {
    $config = [];

    $schedule = new Schedule();

    $sut = new CommandScheduler($schedule);
    $sut->schedule($config);

    assertCommandScheduled($schedule, $command, $expression);
})->with([
    [
        'command' => 'libaro:monitor',
        'expression' => '* * * * *',
    ],
]);

it('schedules correct commands when there are monitored queues', function (string $command, string $expression) {
    $config = [
        'queue' => [
            'test' => 'yes',
        ],
    ];

    $schedule = new Schedule();

    $sut = new CommandScheduler($schedule);
    $sut->schedule($config);

    assertCommandScheduled($schedule, $command, $expression);
})->with([
    [
        'command' => (new DispatchQueueCheckJobsCommand)->getName(),
        'expression' => '* * * * *',
    ],
]);
