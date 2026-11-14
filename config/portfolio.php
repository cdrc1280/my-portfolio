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
        // Per-IP limit for the public form.
        'per_hour' => 3,
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
    | Calendly (admin dashboard: upcoming calls)
    |--------------------------------------------------------------------------
    | A personal access token from Calendly → Integrations → API & Webhooks.
    | Without it the dashboard simply shows how to connect.
    */

    'calendly' => [
        'token' => env('CALENDLY_TOKEN'),
        'cache_minutes' => 10,
        'limit' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics (public pages only, off until configured)
    |--------------------------------------------------------------------------
    | Cookie-free analytics, so no consent banner is needed.
    | - Plausible: ANALYTICS_PROVIDER=plausible, ANALYTICS_SITE_ID=<your domain>
    | - Umami:     ANALYTICS_PROVIDER=umami, ANALYTICS_SITE_ID=<website id>,
    |              ANALYTICS_SCRIPT_URL=<your Umami script.js URL>
    | Custom events (contact brief sent, call booked, résumé downloaded) are sent via
    | resources/js/lib/analytics.ts.
    */

    'analytics' => [
        'provider' => env('ANALYTICS_PROVIDER'),
        'site_id' => env('ANALYTICS_SITE_ID'),
        'script_url' => env('ANALYTICS_SCRIPT_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin account (created by the database seeder)
    |--------------------------------------------------------------------------
    |
    | `sync` is on only when both ADMIN_EMAIL and ADMIN_PASSWORD are set. Then
    | `app:release` copies them onto the existing admin on every deploy, so the
    | fallback login below is never written over a real one.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('ADMIN_PASSWORD', 'password'),
        'sync' => filled(env('ADMIN_EMAIL')) && filled(env('ADMIN_PASSWORD')),
    ],

];
