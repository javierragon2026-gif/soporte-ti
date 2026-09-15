<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

// Programar la sincronización de usuarios con M365 todos los días a las 2:00 AM
use Illuminate\Support\Facades\Schedule;
Schedule::command('sync:m365-users')->dailyAt('02:00');
