<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automazione PDF Spese
Schedule::command('spese:invia-mensili')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/automazione-pdf.log'));
