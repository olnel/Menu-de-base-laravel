<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
Schedule::command('cards:create-monthly-recharges')->monthlyOn(1, '00:00');
//Schedule::command('tenants:run invoices:send-reminders')->dailyAt('08:00');
Schedule::command('tenants:run maintenance:check-alerts')->dailyAt('07:00');
Schedule::command('tenants:run documents:check-expirations')->dailyAt('07:30');
Schedule::command('tenants:run formation:check-alerts')->dailyAt('08:00');
