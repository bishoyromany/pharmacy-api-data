<?php

namespace App\Traits\Sync;

use App\Models\DynamicPOSSTable;
use Illuminate\Support\Facades\Cache;
use App\Traits\Sync\HelpersTrait;
use App\Http\Controllers\POS\TransactionController;
use Illuminate\Http\Request;

trait TransactionsTrait
{
    public function  transactions()
    {
        $cacheKey = "transactions";
        $column = "TransDate";
        $page = 1;
        $perpage = 5000;
        $total = app(DynamicPOSSTable::class)->setTable("POSTransaction")->count();
        if($total < $perpage){
            $total = $perpage * 2;
        }
        $latestRecord = HelpersTrait::getLatestSyncTime($cacheKey);
        $latestRecord = $latestRecord ?? Cache::get($cacheKey) ?? null;
        $dataRes = ['count' => 0, 'res' => []];
        $useMaxDate = false;
        if ($this->all) {
            $latestRecord = null;
        }

        if($latestRecord && strtotime($latestRecord) >= time()){
            $latestRecord = date('Y-m-d', time() - (24*60*60));
        }

        if($total > config("sync.max_allowed_data")){
            $useMaxDate = true;
        }

        if($useMaxDate && (!$latestRecord || strtotime($latestRecord) < config("sync.sync_timestamp"))){
            $latestRecord = config("sync.sync_date");
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
                    'where' => 'POSTransaction.TransDate',
                    'operator' => '>='
                ]);
            }
            $data = (new TransactionController)->index($request);
            $total = $data['pagination']['total'];
            $response = HelpersTrait::sendData($cacheKey, $data['data']->toArray(), $column);
            $page += 1;
            $dataRes['count'] += count($data['data']);
            $dataRes['res'][] = $response;
        }

        return $dataRes;
    }
}
