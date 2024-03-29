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

    protected $useMaxDate = false;

    protected $date = false;

    protected $serverCache = "API_URL";
    protected $dwonDayCache = "DOWN_DAY_CACHE";

    protected $downDay = "2022-04-27";

    public function index($all = false, $resetRX = false)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $this->all = $all;

        $server = Cache::get($this->serverCache) ?? null;

        $downDay = Cache::get($this->dwonDayCache) ?? null;

        /**
         * Reset Server Data Each 24 hours, or if server changed
         */
        if($server !== env($this->serverCache)){
            $this->all = true;
            Cache::put($this->serverCache,env($this->serverCache), now()->addHours(24));
        }

        /**
         * Use Specific Date
         */
        if($this->downDay && $downDay !== $this->downDay){
            $this->date = $this->downDay;
            Cache::put($this->dwonDayCache,$this->downDay, now()->addHours(24 * 80));
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
                'max_date' => true
            ],
            [
                'cacheKey' => 'rx',
                'column' => 'DATEF',
                'table' => 'RxDetails',
                'max_date' => true
            ],
            [
                'cacheKey' => 'rxVaccine',
                'column' => 'VISPresDate',
                'table' => 'RxVaccine',
                'max_date' => true
            ]
        ];
        $response = [];

        HelpersTrait::log("Start Data Sync", true, "Start Data Sync");

        try{
            try{
                $response['transactions'] = $this->transactions();
            }catch(\Exception $e){
                HelpersTrait::log("Finished Data Sync Error Transactions", false, json_encode(['response' => $response, 'error' => $e->getMessage()]));
            }
            foreach($tablesInfo as $table){
                try{
                    $response[$table['cacheKey']] = $this->dataSync($table['cacheKey'], $table['column'], $table['table'], $table['max_date'] ?? false);
                }catch(\Exception $e){
                    HelpersTrait::log("Finished Data Sync Error ".$table['cacheKey'], false, json_encode(['response' => $response, 'error' => $e->getMessage()]));
                }
            }
            try{
                $response['activeDrugs'] = $this->activeDrugs();
            }catch(\Exception $e){
                HelpersTrait::log("Finished Data Sync Error activeDrugs", false, json_encode(['response' => $response, 'error' => $e->getMessage()]));
            }
        }catch(\Exception $e){
            HelpersTrait::log("Finished Data Sync Error", false, json_encode(['response' => $response, 'error' => $e->getMessage()]));
        }

        if($this->test){
            dd($response);
        }

        $res = [
            "success" => true,
            "result" => $response
        ];

        HelpersTrait::log("Finished Data Sync", true, json_encode($res));

        return response()->json($res);
    }

    public function dataSync(string $cacheKey, string $column, string $table, bool $maxDate = false){
        $page = 1;
        $perpage = 3000;
        $total = app(DynamicTable::class)->setTable($table)->count();
        $latestRecord = HelpersTrait::getLatestSyncTime($cacheKey);
        $latestRecord = $latestRecord ?? Cache::get($cacheKey) ?? null;
        $dataRes = ['count' => 0, 'res' => []];

        if($this->date){
            $latestRecord = $this->date;
        }

        if ($this->all) {
            $latestRecord = null;
        }

        if($latestRecord && strtotime($latestRecord) >= time()){
            $latestRecord = date('Y-m-d', time() - (24*60*60));
        }

        if($maxDate && !$this->useMaxDate){
            if($total > config("sync.max_allowed_data")){
                $this->useMaxDate = true;
            }
        }

        if($this->useMaxDate && $maxDate){
            if(!$latestRecord || strtotime($latestRecord) < config("sync.sync_timestamp")){
                $latestRecord = config("sync.sync_date");
            }
        }


        if($total < $perpage){
            $total = $perpage * 2;
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
            if($data['pagination']['total']){
                $total = $data['pagination']['total'];
            }
            $response = HelpersTrait::sendData($cacheKey, $data['data']->toArray(), $column);
            $page += 1;
            $dataRes['count'] += count($data['data']);
            $dataRes['res'][] = $response;
        }

        return $dataRes;
    }
}
