<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Services\WorkMovementService;
use App\Services\UnauthorizedMovementService;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
          $middleware->alias([
            'designation' => \App\Http\Middleware\RoleMiddleware::class,
              ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })  ->withSchedule(function (Schedule $schedule) {
        // Schedule the command to run daily at midnight IST
        $schedule->command('leads:update-expected-month')
                 ->dailyAt('00:00')
                 ->timezone('Asia/Kolkata');

        // Process GPS-based break/lunch penalties every minute.
        $schedule->command('penalties:process-break-lunch')
                 ->everyMinute()
                 ->timezone('Asia/Kolkata')
                 ->withoutOverlapping();

        // Process outside-work movement penalties every minute.
        $schedule->call(function () {
                    app(WorkMovementService::class)->handle();
                 })
                 ->name('work-movements:process-penalties')
                 ->everyMinute()
                 ->timezone('Asia/Kolkata')
                 ->withoutOverlapping();

        // Process unauthorized outside movement penalties every minute.
        $schedule->call(function () {
                    app(UnauthorizedMovementService::class)->handle();
                 })
                 ->name('unauthorized-movements:process-penalties')
                 ->everyMinute()
                 ->timezone('Asia/Kolkata')
                 ->withoutOverlapping();
    })->create();
