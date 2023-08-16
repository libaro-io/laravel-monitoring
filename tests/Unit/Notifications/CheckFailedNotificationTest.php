<?php

use Libaro\LaravelMonitoring\Notifications\CheckFailedNotification;
use NotificationChannels\GoogleChat\Card;
use NotificationChannels\GoogleChat\Enums\Icon;
use NotificationChannels\GoogleChat\GoogleChatMessage;
use NotificationChannels\GoogleChat\Section;
use NotificationChannels\GoogleChat\Widgets\KeyValue;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

test('toGoogleChat returns correct message', function () {
    $checkStub = $this->createStub(Check::class);

    $result1 = Result::make()
        ->check($checkStub)
        ->failed('The message')
        ->meta([
            'meta integer' => 123,
            'meta float' => 123.123,
            'meta bool' => true,
            'meta string' => 'a string',
        ]);

    $result2 = Result::make()
        ->check($checkStub)
        ->failed('The message')
        ->meta([
            'integer meta' => 321,
            'float meta' => 321.321,
            'bool meta' => false,
            'string meta' => 'string a',
        ]);

    $checkResults = [$result1, $result2];

    $expectedMessage = GoogleChatMessage::create()
        ->text('Health checks failed');

    foreach ($checkResults as $result) {
        $card = Card::create()
            ->header(
                $result->check->getLabel().': '.$result->getShortSummary(),
                $result->getNotificationMessage(),
            );

        foreach ($result->meta as $key => $value) {
            $section = Section::create(
                [
                    KeyValue::create(
                        $key,
                        $value,
                    )->icon(Icon::BOOKMARK),
                ],
            );

            $card->section($section);
        }

        $expectedMessage->card($card);
    }

    $sut = new CheckFailedNotification($checkResults);
    $message = $sut->toGoogleChat();

    expect($message)->toEqual($expectedMessage);
});
