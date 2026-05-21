<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan pengecekan deadline setiap jam 08:00 pagi
Schedule::command('reminder:send-deadline')->dailyAt('08:00');