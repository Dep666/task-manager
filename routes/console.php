<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

// Пример команды "inspire"
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Регистрируем расписание после загрузки приложения
app()->booted(function () {
    /** @var Schedule $schedule */
    $schedule = app(Schedule::class);
    // Регистрируем команду 'notify:deadlines' для выполнения каждую минуту
    $schedule->command('notify:deadlines')->everyMinute();
    
    // Регистрируем команду Web Push уведомлений
    $schedule->command('notify:webpush-deadlines')->everyMinute();
});
