<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Runs daily on school days, after the configured absence-check time, and
// marks anyone with no fingerprint scan that day as absent + emails guardians.
// Make sure your server's cron actually calls `php artisan schedule:run` every
// minute (see the README note added for this feature).
Schedule::command('attendance:mark-absent')
    ->weekdays()
    ->dailyAt(config('attendance.absence_check_time', '17:00'));
