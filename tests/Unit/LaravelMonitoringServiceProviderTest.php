<?php

use Libaro\LaravelMonitoring\LaravelMonitoringServiceProvider;
use Libaro\LaravelMonitoring\Services\CheckBuilder;
use Libaro\LaravelMonitoring\Services\CommandScheduler;
use Mockery\MockInterface;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Facades\Health;

test('packageBooted merges config correctly', function () {
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

test('packageBooted removes the default result store', function () {
    $monitoringHealthConfig = [
        'result_stores' => [
            'some other result store' => [
                'some other' => 'some other data',
            ],
        ],
    ];

    $healthConfig = [
        'result_stores' => [
            'a result store' => [
                'some key' => 'some data',
            ],
        ],
    ];

    $expectedHealthConfig = [
        'result_stores' => [
            'some other result store' => [
                'some other' => 'some other data',
            ],
        ],
    ];

    config()->set('health', $healthConfig);
    config()->set('monitoring.health', $monitoringHealthConfig);

    $sut = new LaravelMonitoringServiceProvider($this->app);
    $sut->packageBooted();

    $actualHealthConfig = config('health');

    expect($actualHealthConfig)->toEqual($expectedHealthConfig);
});

test('packageBooted schedules commands', function () {
    $expectedConfig = [
        'config_item' => 'Config value',
        'other_config_item' => 'Other config value',
    ];

    config()->set('monitoring', $expectedConfig);

    $commandSchedulerSpy = $this->spy(CommandScheduler::class);

    $sut = new LaravelMonitoringServiceProvider($this->app);
    $sut->packageBooted();

    $commandSchedulerSpy
        ->shouldHaveReceived('schedule')
        ->once()
        ->with($expectedConfig);
});

test('packageRegistered registers checks', function () {
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
