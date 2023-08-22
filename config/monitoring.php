<?php

// config for Libaro/LaravelMonitoring
return [
    /*
    |--------------------------------------------------------------------------
    | Queue Monitors
    |--------------------------------------------------------------------------
    |
    | This section defines which queues are monitored and which values will
    | trigger a notification, the notifications will be sent through the
    | health package, consult the health config section for more info.
    |
    */

    'queue' => [
        //        'default' => [
        //            'max_wait_minutes' => 5,
        //        ],
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
    |
    | To consult spatie/laravel-health documentation, please visit the webpage
    | https://spatie.be/docs/laravel-health/v1, all configuration available
    | for it can be overridden here, there is no need to use their files.
    |
    */

    'health' => [
        'result_stores' => [
            \Spatie\Health\ResultStores\InMemoryHealthResultStore::class,
        ],

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
