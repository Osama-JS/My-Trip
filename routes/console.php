<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
Schedule::command('app:sync-airports')->dailyAt('02:00');
Schedule::command('app:sync-hotel-cities')->weeklyOn(0, '04:00');
