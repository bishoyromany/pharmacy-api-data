<?php

namespace App\Traits\Sync;

use App\Models\Pharmacy;
use App\Models\Transactions;
use GuzzleHttp\Client;

trait TransactionsTrait
{
    public function  transactions(Pharmacy $pharmacy)
    {
        $client = new Client;
        $page = 1;
        $perpage = 5000;
        $total = 20000;
        $latestRecord = Transactions::where('pharmacy_id', $pharmacy->id)->orderBy('TransDate', 'desc')->select('TransDate')->first()->TransDate ?? null;

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
                $query['where'] = 'POSTransaction.TransDate';
                $query['operator'] = ">=";
            }

            $response = $client->get($pharmacy->config->api . "/transactions", [
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
                foreach ($this->transactionsColumns as $column) {
                    $storeData[$column] = $item->{$column};
                }
                $storeData['pharmacy_id'] = $pharmacy->id;
                $storeData['user_id'] = $pharmacy->user_id;
                $storeData['data'] = json_encode($item);
                $storeData['hash'] = md5(json_encode($storeData));
                $storeData['payment_type'] = $item->paymentTypes->PayTypeDesc ?? "Not Found";

                Transactions::updateOrCreate(
                    ['TransID' => $storeData['TransID'], 'pharmacy_id' => $pharmacy->id],
                    $storeData
                );
            });
        }
    }
}
