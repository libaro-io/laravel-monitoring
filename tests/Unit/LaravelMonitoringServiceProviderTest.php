<?php

use Illuminate\Console\Application;
use Illuminate\Console\Scheduling\Schedule;
use Libaro\LaravelMonitoring\LaravelMonitoringServiceProvider;
use Libaro\LaravelMonitoring\Services\CheckBuilder;
use Mockery\MockInterface;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Facades\Health;

it('merges config correctly', function () {
    $monitoringHealthConfig = [
        'a duplicate single value' => 'the single value (altered)',
        'another single value' => 'the other single value',
        'a duplicate key' => [
            'a duplicate key > a nested single value' => 'a duplicate key > the nested single value (altered)',
        ],
    ];

    $healthConfig = [
        'a duplicate single value' => 'the single value',
        'a non duplicate single value' => 'the non duplicate single value',
        'a duplicate key' => [
            'a duplicate key > a nested single value' => 'a duplicate key > the nested single value',
            'a duplicate key > another nested single value' => 'a duplicate key > the other nested single value',
        ],
        'a non duplicate key' => [
            'a non duplicate key > a nested single value' => 'a non duplicate key > the nested single value',
        ],
    ];

    $expectedHealthConfig = [
        'a duplicate single value' => 'the single value (altered)',
        'another single value' => 'the other single value',
        'a non duplicate single value' => 'the non duplicate single value',
        'a duplicate key' => [
            'a duplicate key > a nested single value' => 'a duplicate key > the nested single value (altered)',
            'a duplicate key > another nested single value' => 'a duplicate key > the other nested single value',
        ],
        'a non duplicate key' => [
            'a non duplicate key > a nested single value' => 'a non duplicate key > the nested single value',
        ],
    ];

    config()->set('health', $healthConfig);
    config()->set('monitoring.health', $monitoringHealthConfig);

    $sut = new LaravelMonitoringServiceProvider($this->app);
    $sut->packageBooted();

    $actualHealthConfig = config('health');

    expect($actualHealthConfig)->toEqual($expectedHealthConfig);
});

test('schedule has correct entries for commands', function (string $command, string $expression) {
    $sut = new LaravelMonitoringServiceProvider($this->app);
    $sut->packageBooted();

    /** @var Schedule $schedule */
    $schedule = app(Schedule::class);

    $itemsWithCommand = collect($schedule->events())
        ->where('command', Application::formatCommandString($command));

    expect($itemsWithCommand)
        ->not->toBeEmpty(sprintf('Failed asserting that the "%s" command was scheduled', $command));

    $itemsWithCommandAndExpression = $itemsWithCommand
        ->where('expression', $expression);

    expect($itemsWithCommandAndExpression)
        ->not->toBeEmpty(sprintf('Failed asserting that the "%s" command was scheduled with expression "%s"', $command, $expression));
})->with([
    [
        'command' => 'libaro:monitor',
        'expression' => '* * * * *',
    ],
    [
        'command' => (new DispatchQueueCheckJobsCommand)->getName(),
        'expression' => '* * * * *',
    ],
]);

it('registers checks', function () {
    $expectedConfig = [
        'config_item' => 'Config value',
        'other_config_item' => 'Other config value',
    ];

    config()->set('monitoring', $expectedConfig);

    $expectedChecks = [
        QueueCheck::new(),
        DatabaseCheck::new(),
    ];

    $this->mock(CheckBuilder::class, function (CheckBuilder&MockInterface $mock) use ($expectedChecks, $expectedConfig) {
        $mock
            ->shouldReceive('build')
            ->with($expectedConfig)
            ->once()
            ->andReturns($expectedChecks);
    });

    Health::spy();

    $sut = new LaravelMonitoringServiceProvider($this->app);
    $sut->packageRegistered();

    Health::shouldHaveReceived('checks')
        ->with($expectedChecks)
        ->once();
});
