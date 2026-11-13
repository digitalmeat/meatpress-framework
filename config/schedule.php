<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Scheduling driver
    |--------------------------------------------------------------------------
    |
    | Where scheduled events are kept and who runs them: #[Schedule],
    | Schedule::event(), and every wp_schedule_event() call of WordPress, a
    | plugin or a theme.
    |
    | "wp-cron" is WordPress's own and needs nothing. "action-scheduler" stores
    | events in Action Scheduler (a plugin that bundles it, such as WooCommerce,
    | must be active); without it, events stay on WP-Cron.
    |
    | Changing the driver moves nothing: events already scheduled stay where they
    | are until php artisan meatpress:schedule:migrate moves them, and
    | meatpress:doctor reminds you.
    |
    | "laravel" makes each event a job delayed until it is due, in the
    | application's queue: it needs a connection that accepts delays (not
    | "sync" or "null") and a worker, php artisan queue:work. Nothing else
    | runs the events, so without a worker none of them fires.
    |
    | Supported: "wp-cron", "action-scheduler", "laravel"
    |
    */

    'driver' => env('MEATPRESS_SCHEDULE_DRIVER', 'wp-cron'),

    /*
    |--------------------------------------------------------------------------
    | The "laravel" driver
    |--------------------------------------------------------------------------
    |
    | connection: the queue connection of the jobs; null is the application's
    | default. On a multisite network every site uses the same one, with a
    | single worker.
    |
    | queue: the queue they wait in; null is the connection's default.
    |
    | max_delay: the longest delay, in seconds, the connection accepts. A job
    | due later waits again when it wakes. null: 900 for SQS, none otherwise.
    |
    | late_after: an event this many seconds past its time is queued again
    | (same identifier, so it never runs twice) when its job was lost.
    | Checked every five minutes, or with php artisan meatpress:schedule:recover.
    |
    */

    'laravel' => [
        'connection' => env('MEATPRESS_SCHEDULE_QUEUE_CONNECTION'),
        'queue' => env('MEATPRESS_SCHEDULE_QUEUE'),
        'max_delay' => env('MEATPRESS_SCHEDULE_MAX_DELAY'),
        'late_after' => (int) env('MEATPRESS_SCHEDULE_LATE_AFTER', 600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Hooks that stay on WP-Cron
    |--------------------------------------------------------------------------
    |
    | Action Scheduler's own queue runner always does: handed to Action
    | Scheduler, it would never run. Add hooks that must run by WP-Cron
    | whatever the driver. The meatpress/schedule/protected_hooks filter extends
    | the list too.
    |
    */

    'protected_hooks' => [],

];
