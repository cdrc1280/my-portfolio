<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contact form
    |--------------------------------------------------------------------------
    | Address that receives a (queued) email for every contact message.
    */

    'contact' => [
        'notify_email' => env('CONTACT_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS')),
        // Per-IP limits for the public form.
        'per_minute' => 3,
        'per_day' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | GitHub activity section
    |--------------------------------------------------------------------------
    */

    'github' => [
        'username' => env('GITHUB_USERNAME'),
        'token' => env('GITHUB_TOKEN'),
        'cache_minutes' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin account (created by the database seeder)
    |--------------------------------------------------------------------------
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],

];
