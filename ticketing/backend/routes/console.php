<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Holds tipicos duran 10 minutos; revisar cada minuto los libera casi de
// inmediato para que el cupo vuelva a estar disponible para otros compradores.
Schedule::command('tickets:expire-holds')->everyMinute()->withoutOverlapping();
