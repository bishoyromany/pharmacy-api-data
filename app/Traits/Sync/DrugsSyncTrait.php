<?php

namespace App\Traits\Sync;

use App\Models\RX;
use App\Models\DynamicTable;
use Illuminate\Support\Facades\Cache;
use App\Traits\Sync\HelpersTrait;
use App\Http\Controllers\DynamicTableController;
use Illuminate\Http\Request;

trait DrugsSyncTrait
{
    public function activeDrugs()
    {
        $dataRes = ['count' => 0, 'res' => []];
        $cacheKey = "activeDrugs";
        $column = "DRGNDC";
        $table = "DRUG";
        $page = 1;
        $perpage = 1000;
        $ndcs = [[]];
        $index = 0;

        RX::select('NDC')->distinct('NDC')->get()->map(function ($item) use (&$ndcs, &$index, $perpage) {
                if (count($ndcs[$index]) < $perpage) {
                    $ndcs[$index][] = $item->NDC;
                } else {
                    $index++;
                    $ndcs[$index][] = $item->NDC;
                }
            });

        foreach ($ndcs as $ndc) {
            $request = new Request;
            $request->merge([
                'perpage' => $perpage,
                'page' => $page,
                'json' => false,
                'where' => 'DRGNDC',
                'operator' => 'in',
                'value' => implode(',', $ndc)
            ]);
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
