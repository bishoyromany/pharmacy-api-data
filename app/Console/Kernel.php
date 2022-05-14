<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\PharmacySync;
use App\Jobs\Update;
use App\Jobs\PrefillRXSync;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\PharmacySync::class,
        Commands\Update::class,
        Commands\PrefillRXSync::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new PharmacySync)->everyThirtyMinutes()->name("Data Sync")->withoutOverlapping();
        $schedule->job(new Update)->everyTenMinutes()->name("System Update");
        $schedule->job(new PrefillRXSync)->daily()->name("Prefill RX Sync");
        $schedule->call(function () {
            \Log::info('Cron Alive ' . date('Y-m-d H:i:s A'));
        })->everyTenMinutes()->name("Cron Is Active");
        $schedule->call(function () {
            \Artisan::call('schedule:clear-cache');
        })->hourly()->name("Clear Mutex");
        // $schedule->command('update')->everyMinute()->withoutOverlapping();
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
