<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reset leave balances every January 1st at midnight
Schedule::command('leave:reset-balances')->yearlyOn(1, 1, '00:00');

// Check subscriptions daily
Schedule::command('subscriptions:check')->dailyAt('08:00');


