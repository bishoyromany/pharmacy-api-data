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
        $perpage = 10000;
        $total = app(DynamicPOSSTable::class)->setTable("POSTransaction")->count();
        $latestRecord = Cache::get($cacheKey) ?? null;

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
                    'where' => 'POSTransaction.TransDate',
                    'operator' => '>='
                ]);
            }
            $data = (new TransactionController)->index($request);
            $response = HelpersTrait::sendData($cacheKey, $data['data']->toArray(), $column);
            $page += 1;
        }
    }
}
