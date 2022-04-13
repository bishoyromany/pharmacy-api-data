<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Sync\HelpersTrait;

class LatestDataSyncController extends Controller
{
    use HelpersTrait;

    public function getLatestTime(Request $request){
        $tables = ["transactions", "patients", "rxPay", "rx", "rxVaccine"];
        $data = [];
        foreach($tables as $table){
            $data[$table] = $this->getLatestSyncTime($table);
        }
        dd($data);
    }
}
