<?php

use Illuminate\Support\Facades\Schedule;

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

Schedule::command(
    command: 'queue:retry',
    parameters: ['all'],
)->everyTenMinutes();
