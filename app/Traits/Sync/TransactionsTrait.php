<?php

namespace App\Traits\Sync;

use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use App\Traits\Sync\HelpersTrait;
trait TransactionsTrait
{
    public function  transactions()
    {
        $cacheKey = "transactions";
        $column = "TransDate";
        $page = 1;
        $perpage = 100;
        $total = Transaction::count();
        $latestRecord = Cache::get($cacheKey) ?? null;

        if ($this->all) {
            $latestRecord = null;
        }

        while ($page * $perpage <= $total + $perpage) {
            $query = new Transaction;
            if($latestRecord){
                $query = $query->where($column, '>', $latestRecord);
            }
            $data = $query->limit($perpage)->offset($perpage * $page)->get()->map(function($item){
                return json_decode($item->data);
            });
            $response = HelpersTrait::sendData($cacheKey, $data->toArray(), $column);
            $page += 1;
        }
    }
}
