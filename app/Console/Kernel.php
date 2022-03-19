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
        $schedule->call(function () {
            \Log::info('Cron Alive ' . date('Y-m-d H:i:s A'));
        })->everyMinute()->name("Cron Is Active");
        $schedule->job(new PharmacySync)->everyThirtyMinutes()->name("Data Sync")->withoutOverlapping();
        $schedule->job(new Update)->everySixHours()->name("System Update")->withoutOverlapping();
        // Updated
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
