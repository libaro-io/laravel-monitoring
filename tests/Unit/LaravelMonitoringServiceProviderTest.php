<?php

use Libaro\LaravelMonitoring\LaravelMonitoringServiceProvider;

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
