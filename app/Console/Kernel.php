<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\PharmacySync;
use App\Jobs\Update;

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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {   
        $schedule->job(new PharmacySync)->everyTenMinutes()->name("Data Sync")->withoutOverlapping();
        $schedule->job(new Update)->everyTenMinutes()->name("System Update");
        $schedule->call(function () {
            \Log::info('Cron Alive ' . date('Y-m-d H:i:s A'));
        })->everyTenMinutes()->name("Cron Is Active");
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
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
