<?php

use Illuminate\Support\Facades\Notification as NotificationFacade;
use Libaro\LaravelMonitoring\Commands\MonitorHealthCommand;
use Libaro\LaravelMonitoring\Notfiables\CustomNotifiable;
use Libaro\LaravelMonitoring\Notifications\CheckFailedNotification;
use Mockery\MockInterface;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use function Pest\Laravel\artisan;

it('has correct signature', function () {
    $sut = new MonitorHealthCommand();
    $name = $sut->getName();

    expect($name)->toEqual('libaro:monitor:health');
});

it('extends correct class', function () {
    $sut = new MonitorHealthCommand();

    expect($sut)->toBeInstanceOf(\Spatie\Health\Commands\RunHealthChecksCommand::class);
});

it('sends correct notification', function () {
    NotificationFacade::fake();

    $check = $this->mock(Check::class, function (Check&MockInterface $mock) {
        $result = Result::make()
            ->check($mock)
            ->failed('The message');
        $mock->allows('run')
            ->andReturn($result);
        $mock->allows('shouldRun')
            ->andReturn(true);
        $mock->allows('getLabel')
            ->andReturn('The label');
        $mock->allows('getName')
            ->andReturn('The name');
    });

    \Spatie\Health\Facades\Health::checks([
        $check,
    ]);

    artisan(MonitorHealthCommand::class, ['--do-not-store-results' => true])
        ->assertSuccessful();

    NotificationFacade::assertSentTo(
        new CustomNotifiable(),
        fn (CheckFailedNotification $notification) => true,
    );
});
