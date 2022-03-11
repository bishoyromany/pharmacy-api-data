<?php

namespace App\Traits\Sync;

use App\Models\DynamicTable;
use Illuminate\Support\Facades\Cache;
use App\Traits\Sync\HelpersTrait;
use App\Http\Controllers\DynamicTableController;
use Illuminate\Http\Request;

trait RXSyncTrait
{
    public function rxVaccine(Pharmacy $pharmacy, $resetRX = false)
    {
        $cacheKey = "rxVaccine";
        $column = "VISPresDate";
        $table = "RxVaccine";
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

    public function rxPay(Pharmacy $pharmacy, $resetRX = false)
    {
        $client = new Client;
        $page = 1;
        $perpage = 10000;
        $total = $page * 20000;
        if ($resetRX) {
            RXPay::where('pharmacy_id', $pharmacy->id)->delete();
        }
        $latestRecord = RXPay::where('pharmacy_id', $pharmacy->id)->orderBy('DATEPAID', 'desc')->select('DATEPAID')->first()->DATEPAID ?? null;

        if ($this->all) {
            $latestRecord = null;
        }

        while ($page * $perpage <= $total + $perpage) {
            $query = [
                'perpage' => $perpage,
                'page' => $page,
                'password' => $pharmacy->config->api_password
            ];

            if ($latestRecord) {
                $query['value'] = $latestRecord;
                $query['where'] = 'DATEPAID';
                $query['operator'] = ">=";
            }


            $response = $client->get($pharmacy->config->api . "/RxPay", [
                'query' => $query
            ]);

            $data = json_decode($response->getBody()->getcontents());
            $pagination = $data->pagination;
            $data = $data->data;

            $page = $pagination->nextPage;
            $perpage = $pagination->perpage;
            $total = $pagination->total;

            collect($data)->map(function ($item) use ($pharmacy) {
                $storeData = [];
                foreach ($this->rxPayColumns as $column) {
                    $storeData[$column] = $item->{$column} ?? $column;
                }
                $storeData['pharmacy_id'] = $pharmacy->id;
                $storeData['user_id'] = $pharmacy->user_id;
                $storeData['data'] = $item;

                RXPay::updateOrCreate(
                    [
                        'RXNO' => $storeData['RXNO'], 'REFILL_NO' => $storeData['REFILL_NO'],
                        'pharmacy_id' => $pharmacy->id, 'COVERAGECD' => $storeData['COVERAGECD'],
                        'INS_CODE' => $storeData['INS_CODE']
                    ],
                    $storeData
                );
            });
        }
    }

    public function rx(Pharmacy $pharmacy, $resetRX = false)
    {
        $client = new Client;
        $page = 1;
        $perpage = 10000;
        $total = $page * 20000;
        if ($resetRX) {
            RX::where('pharmacy_id', $pharmacy->id)->delete();
        }
        $latestRecord = RX::where('pharmacy_id', $pharmacy->id)->orderBy('DATEF', 'desc')->select('DATEF')->where('STATUS', 'B')->where('DATEF', '<=', date('Y-m-d'))->first()->DATEF ?? null;

        if ($this->all) {
            $latestRecord = null;
        }

        while ($page * $perpage <= $total + $perpage) {
            $query = [
                'perpage' => $perpage,
                'page' => $page,
                'password' => $pharmacy->config->api_password
            ];

            if ($latestRecord) {
                $query['value'] = $latestRecord;
                $query['where'] = 'DATEF';
                $query['operator'] = ">=";
            }

            $response = $client->get($pharmacy->config->api . "/RxDetails", [
                'query' => $query
            ]);

            $data = json_decode($response->getBody()->getcontents());
            $pagination = $data->pagination;
            $data = $data->data;

            $page = $pagination->nextPage;
            $perpage = $pagination->perpage;
            $total = $pagination->total;

            collect($data)->map(function ($item) use ($pharmacy) {
                $storeData = [];
                foreach ($this->rxColumns as $column) {
                    $storeData[$column] = $item->{$column} ?? $column;
                }
                $storeData['pharmacy_id'] = $pharmacy->id;
                $storeData['user_id'] = $pharmacy->user_id;
                $storeData['patient_id'] = Patient::where('pharmacy_id', '=', $pharmacy->id)->where('PATIENTNO', '=', $storeData['PATIENTNO'])->get()[0]->id ?? 0;
                $storeData['data'] = $item;
                $storeData['created_at'] = $item->DATEF;
                $storeData['updated_at'] = $item->DATEF;

                RX::updateOrCreate(
                    [
                        'RXNO' => $storeData['RXNO'], 'pharmacy_id' => $pharmacy->id,
                        'NREFILL' => $storeData['NREFILL']
                    ],
                    $storeData
                );
            });
        }
    }
}
