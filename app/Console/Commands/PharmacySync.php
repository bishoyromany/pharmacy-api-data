<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PharmacySyncController;

class PharmacySync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pharmacy:sync {--all=} {--reset-rx=} {--reset-transactions=} {--reset-all=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync All Pharmacy Data';

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
        (new PharmacySyncController)->index($this->option("all"), $this->option("reset-rx"));
        return Command::SUCCESS;
    }
}
