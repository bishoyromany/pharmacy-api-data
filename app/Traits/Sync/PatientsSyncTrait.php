<?php

namespace App\Traits\Sync;

use App\Models\DynamicTable;
use Illuminate\Support\Facades\Cache;
use App\Traits\Sync\HelpersTrait;
use App\Http\Controllers\DynamicTableController;
use Illuminate\Http\Request;

trait PatientsSyncTrait
{
    public function patients()
    {
        $cacheKey = "patients";
        $column = "LASTMODIFIED";
        $table = "PATIENT";
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
            return $response;
            $page += 1;
            $dataRes['count'] += count($data['data']);
            $dataRes['res'][] = $response;
        }

        return $dataRes;
    }
}
