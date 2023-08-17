<?php

use Libaro\LaravelMonitoring\Services\CheckBuilder;
use Spatie\Health\Checks\Checks\QueueCheck;

it('builds checks for queue', function () {
    $config = [
        'queue' => [
            'test-queue' => [
                'max_wait_minutes' => 5,
            ],
            'other' => [
                'max_wait_minutes' => 20,
            ],
        ],
    ];

    $expectedChecks = [
        QueueCheck::new()
            ->name('Queue "test-queue"')
            ->onQueue('test-queue')
            ->failWhenHealthJobTakesLongerThanMinutes(5),
        QueueCheck::new()
            ->name('Queue "other"')
            ->onQueue('other')
            ->failWhenHealthJobTakesLongerThanMinutes(20),
    ];

    $sut = new CheckBuilder();
    $checks = $sut->build($config);

    expect($checks)->toEqualCanonicalizing($expectedChecks);
});

it('does not build checks for queue if there are none configured', function () {
    $config = [
        'queue' => [],
    ];

    $sut = new CheckBuilder();
    $checks = $sut->build($config);

    expect($checks)
        ->toBeArray()
        ->toBeEmpty();
});
