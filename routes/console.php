<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Menjalankan pembebasan tiket kedaluwarsa setiap menit
Schedule::command('tickets:release-expired')->everyMinute();

// Menjalankan pengingat transaksi abandoned cart setiap 5 menit
Schedule::command('recovery:cart')->everyFiveMinutes();