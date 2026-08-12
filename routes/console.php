<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backup diario de la base de datos a las 03:00. Mantiene los últimos días
// según config/backup.php (retención) y borra los más antiguos solo.
Schedule::command('db:backup')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onFailure(function () {
        logger()->error('El backup diario de la base de datos falló.');
    });
