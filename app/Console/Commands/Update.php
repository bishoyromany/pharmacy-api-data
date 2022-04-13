<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\Sync\HelpersTrait;
class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Local System';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = exec(__DIR__."/../../../update.bat");
        HelpersTrait::log("System Update", true, $data);
        return 0;
    }
}
