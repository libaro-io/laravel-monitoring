<?php

// config for Libaro/LaravelMonitoring
return [
    'health' => [
        'notifications' => [
            'notifiable' => \Libaro\LaravelMonitoring\Notfiables\CustomNotifiable::class,

            'notifications' => [
                \Libaro\LaravelMonitoring\Notifications\CheckFailedNotification::class => [
                    \NotificationChannels\GoogleChat\GoogleChatChannel::class,
                ],
            ],

            \NotificationChannels\GoogleChat\GoogleChatChannel::class => [
                'space' => null,
            ],
        ],
    ],
];
