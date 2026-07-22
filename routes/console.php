<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Tâches planifiées — Clinique Tamarix
|--------------------------------------------------------------------------
| Une seule entrée cron suffit sur le serveur :
|   * * * * * cd /chemin/du/projet && php artisan schedule:run >> /dev/null 2>&1
*/

// Rappel J-1 des rendez-vous confirmés, chaque jour à 10h00
Schedule::command('tamarix:rappels-j1')
    ->dailyAt('10:00')
    ->timezone('Africa/Abidjan')
    ->withoutOverlapping()
    ->onOneServer();

// Clôture des rendez-vous échus, chaque nuit à 01h00
Schedule::command('tamarix:cloturer-rdv-passes')
    ->dailyAt('01:00')
    ->timezone('Africa/Abidjan')
    ->withoutOverlapping()
    ->onOneServer();

// Purge des journaux d'audit de plus d'un an, chaque dimanche
Schedule::command('model:prune', ['--model' => \App\Models\ActivityLog::class])
    ->weeklyOn(7, '02:00')
    ->timezone('Africa/Abidjan');
