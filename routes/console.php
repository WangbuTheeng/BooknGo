<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the expired bookings cleanup to run every 15 minutes
Schedule::command('bookings:cleanup-expired')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();
