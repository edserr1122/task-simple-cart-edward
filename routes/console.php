<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily sales report to run every evening at 8 PM
Schedule::command('sales:generate-daily-report')
    ->dailyAt('20:00')
    ->withoutOverlapping()
    ->runInBackground();
