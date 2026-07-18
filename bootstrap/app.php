<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    // START: Tambahkan bagian withSchedule ini
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('agreements:update-statuses')->daily(); // Jalankan setiap hari
        // Atau $schedule->command('agreements:update-statuses')->dailyAt('00:00'); // Jalankan setiap hari pukul 00:00
    })
    // END: Tambahkan bagian withSchedule ini
    ->create();
