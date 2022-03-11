<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Sync\RXSyncTrait;
use App\Traits\Sync\DrugsSyncTrait;
use App\Traits\Sync\PatientsSyncTrait;
use App\Traits\Sync\InsurancesSyncTrait;
use App\Traits\Sync\TransactionsTrait;

class PharmacySyncController extends Controller
{
    use RXSyncTrait, DrugsSyncTrait, PatientsSyncTrait, InsurancesSyncTrait, TransactionsTrait;

    protected $all = false;

    protected $test = true;

    public function index($all = false, $resetRX = false)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $this->all = $all;
        $response = [];
        $response['transactions'] = $this->transactions();
        $response['patients'] = $this->patients();
        if($this->test){
            dd($response);
        }
        $this->rxPay( $resetRX);
        $this->rx($resetRX);
        $this->rxVaccine($resetRX);
        $this->activeDrugs();
        return response()->json([
            "success" => true,
            "result" => $response
        ]);
    }
}
