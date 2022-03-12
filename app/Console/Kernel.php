<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\PharmacySync;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\PharmacySync::class,
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
            if (!file_exists(base_path() . "/cron.log")) {
                file_put_contents(base_path() . "/cron.log", "");
            }
            $fp = fopen(base_path() . "/cron.log", 'a'); //opens file in append mode  
            fwrite($fp, 'Cron Alive ' . date('Y-m-d H:i:s A') . " \n");
            fclose($fp);
        })->everyMinute()->name("Cron Is Active");
        $schedule->job(new PharmacySync)->everyTenMinutes()->name("Data Sync");
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
