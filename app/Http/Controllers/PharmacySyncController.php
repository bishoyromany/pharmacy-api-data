<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Sync\RXSyncTrait;
use App\Traits\Sync\DrugsSyncTrait;
use App\Traits\Sync\PatientsSyncTrait;
use App\Traits\Sync\InsurancesSyncTrait;
use App\Traits\Sync\TransactionsTrait;
use App\Models\DynamicTable;
use Illuminate\Support\Facades\Cache;
use App\Traits\Sync\HelpersTrait;
use App\Http\Controllers\DynamicTableController;
use Illuminate\Http\Request;

class PharmacySyncController extends Controller
{
    use RXSyncTrait, DrugsSyncTrait, PatientsSyncTrait, InsurancesSyncTrait, TransactionsTrait;

    protected $all = false;

    protected $test = false;

    protected $serverCache = "API_URL";

    public function index($all = false, $resetRX = false)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $this->all = $all;

        $server = Cache::get($this->serverCache) ?? null;

        /**
         * Reset Server Data Each 24 hours, or if server changed
         */
        if($server !== env($this->serverCache)){
            $this->all = true;
            Cache::put($this->serverCache,env($this->serverCache), now()->addHours(24));
        }

        $tablesInfo = [
            [
                'cacheKey' => 'patients',
                'column' => 'LASTMODIFIED',
                'table' => 'PATIENT',
            ],
            [
                'cacheKey' => 'rxPay',
                'column' => 'DATEPAID',
                'table' => 'RxPay',
            ],
            [
                'cacheKey' => 'rx',
                'column' => 'DATEF',
                'table' => 'RxDetails',
            ],
            [
                'cacheKey' => 'rxVaccine',
                'column' => 'VISPresDate',
                'table' => 'RxVaccine',
            ]
        ];
        $response = [];

        try{
            $response['transactions'] = $this->transactions();
            foreach($tablesInfo as $table){
                $response[$table['cacheKey']] = $this->dataSync($table['cacheKey'], $table['column'], $table['table']);
            }
            $response['activeDrugs'] = $this->activeDrugs();
        }catch(\Exception $e){
            \Log::error("Failed Data Sync", ['response' => json_encode($response), 'error' => $e->getMessage()]);
        }

        if($this->test){
            dd($response);
        }

        $res = [
            "success" => true,
            "result" => $response
        ];

        \Log::info("Success Data Sync", ['response' => json_encode($res)]);

        return response()->json($res);
    }

    public function dataSync(string $cacheKey, string $column, string $table){
        $page = 1;
        $perpage = 1000;
        $total = app(DynamicTable::class)->setTable($table)->count();
        $latestRecord = Cache::get($cacheKey) ?? null;
        $dataRes = ['count' => 0, 'res' => []];
        if ($this->all) {
            $latestRecord = null;
        }

        while ($page * $perpage <= $total + $perpage) {
            $request = new Request;
            $request->merge([
                'perpage' => $perpage,
                'page' => $page,
                'json' => false
            ]);
            if($latestRecord){
                $request->merge([
                    'value' => $latestRecord,
                    'where' => $column,
                    'operator' => '>=',
                    'order' => 'ASC',
                    'orderBy' => $column
                ]);
            }
            $data = (new DynamicTableController)->index($table, $request);
            $total = $data['pagination']['total'];
            $response = HelpersTrait::sendData($cacheKey, $data['data']->toArray(), $column);
            $page += 1;
            $dataRes['count'] += count($data['data']);
            $dataRes['res'][] = $response;
        }

        return $dataRes;
    }
}
