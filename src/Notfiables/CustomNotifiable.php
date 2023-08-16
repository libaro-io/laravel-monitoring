<?php

namespace Libaro\LaravelMonitoring\Notfiables;

use NotificationChannels\GoogleChat\GoogleChatChannel;
use Spatie\Health\Notifications\Notifiable;

class CustomNotifiable extends Notifiable
{
    public function routeNotificationForGoogleChat(): string|array
    {
        return config('health.notifications.' . GoogleChatChannel::class . '.space');
    }
}
