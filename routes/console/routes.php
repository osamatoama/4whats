<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command(command: 'backup:clean', parameters: ['--only-db' => true])->dailyAt(time: '01:00');
Schedule::command(command: 'backup:run', parameters: ['--only-db' => true])->dailyAt(time: '01:30');
