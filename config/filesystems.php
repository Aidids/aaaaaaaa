<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'profile' => [
            'driver' => 'local',
            'root' => storage_path('app/profile'),
            'url' => env('APP_URL').'/profile',
            'visibility' => 'public',
            'throw' => false,
        ],

        'leave-request' => [
            'driver' => 'local',
            'root' => storage_path('app/leave-request'),
            'url' => env('APP_URL').'/leave-request',
            'visibility' => 'public',
            'throw' => false,
        ],

        'redeem-replacement-leave' => [
            'driver' => 'local',
            'root' => storage_path('app/redeem-replacement-leave'),
            'url' => env('APP_URL').'/redeem-replacement-leave',
            'visibility' => 'public',
            'throw' => false,
        ],

        'redeem-offshore-leave' => [
            'driver' => 'local',
            'root' => storage_path('app/redeem-offshore-leave'),
            'url' => env('APP_URL').'/redeem-offshore-leave',
            'visibility' => 'public',
            'throw' => false,
        ],

        'travel-authorization' => [
            'driver' => 'local',
            'root' => storage_path('app/e-form/travel-authorization'),
            'url' => env('APP_URL').'/storage/app/e-form/travel-authorization',
            'visibility' => 'public',
            'throw' => false,
        ],

        'travel-claim-attachment' => [
            'driver' => 'local',
            'root' => storage_path('app/travel-claim-attachment'),
            'url' => env('APP_URL').'/travel-claim-attachment',
            'visibility' => 'public',
            'throw' => false,
        ],

        'personal-attachment' => [
            'driver' => 'local',
            'root' => storage_path('app/personal-attachment'),
            'url' => env('APP_URL').'/personal-attachment',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('profile') => storage_path('app/profile'),
        public_path('leave-request') => storage_path('app/leave-request'),
        public_path('redeem-replacement-leave') => storage_path('app/redeem-replacement-leave'),
        public_path('redeem-offshore-leave') => storage_path('app/redeem-offshore-leave'),
        public_path('travel-claim-attachment') => storage_path('app/travel-claim-attachment'),
        public_path('personal-attachment') => storage_path('app/personal-attachment'),

        public_path('e-form') => storage_path('app/e-form'),
    ],
];
