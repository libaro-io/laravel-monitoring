<?php

namespace Libaro\LaravelMonitoring\Notifications;

use NotificationChannels\GoogleChat\Card;
use NotificationChannels\GoogleChat\Enums\Icon;
use NotificationChannels\GoogleChat\GoogleChatMessage;
use NotificationChannels\GoogleChat\Section;
use NotificationChannels\GoogleChat\Widgets\KeyValue;

class CheckFailedNotification extends \Spatie\Health\Notifications\CheckFailedNotification
{
    public function toGoogleChat(): GoogleChatMessage
    {
        $googleChatMessage = GoogleChatMessage::create()
            ->text('Health checks failed');

        foreach ($this->results as $result) {
            $card = Card::create()
                ->header(
                    $result->check->getLabel(),
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

            $googleChatMessage->card($card);
        }

        return $googleChatMessage;
    }
}
