<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Schedule::command(
    command: 'queue:work --stop-when-empty',
)
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command(
    command: 'queue:work --queue=subscriptions --stop-when-empty',
)
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command(
    command: 'queue:work --queue=orders --stop-when-empty',
)
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command(
    command: 'queue:work --queue=customers --stop-when-empty',
)
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command(
    command: 'queue:work --queue=abandoned-carts --stop-when-empty',
)
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command(
    command: 'queue:restart',
)
    ->everyFiveMinutes();

Schedule::command(
    command: 'backup:clean',
)->dailyAt(
    time: '01:00',
);

Schedule::command(
    command: 'backup:run',
    parameters: ['--only-db'],
)->dailyAt(
    time: '01:30',
);

Artisan::command(
    signature: 'queue:retry-too-many-requests-failed-jobs',
    callback: function (): void {
        DB::table(
            table: config(
                key: 'queue.failed.table',
            ),
        )->where(
            column: 'exception',
            operator: 'LIKE',
            value: '%Too Many Requests%',
        )->get()->each(
            callback: function (object $failedJob) {
                Artisan::call(
                    command: "queue:retry {$failedJob->uuid}",
                );
            },
        );
    },
)->everyMinute();
