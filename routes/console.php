<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * The monthly report reaches the site address on the first Sunday of each month (Maracay time).
 */
Schedule::command('reports:send-monthly')->weeklyOn(0, '08:00')->when(fn (): bool => now()->day <= 7);
