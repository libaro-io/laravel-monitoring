<?php

// config for Libaro/LaravelMonitoring
return [
    'queue' => [
        'default' => [
            // A test job will be dispatched every minute, when it didn't run for longer
            // this value, a notification will be triggered
            'max_wait_minutes' => 5,
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Health Config Overrides
    |--------------------------------------------------------------------------
    |
    | This package utilizes the spatie/laravel-health package to perform checks
    | You can choose, either change your health configuration here or in the
    | spatie packages health.php, this one has precedence over health.php.
    |
    */

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
