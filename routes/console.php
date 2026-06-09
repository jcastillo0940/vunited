<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Primer equipo (Veraguas United FC) — lunes 3:00 AM
Schedule::command('squad:import-transfermarkt --club=48217 --category=first-team --fresh')
    ->weekly()->mondays()->at('03:00')
    ->withoutOverlapping()->runInBackground();

// Segundo equipo / Academia (Veraguas CD II) — lunes 3:30 AM
Schedule::command('squad:import-transfermarkt --club=94117 --category=academy --fresh')
    ->weekly()->mondays()->at('03:30')
    ->withoutOverlapping()->runInBackground();
