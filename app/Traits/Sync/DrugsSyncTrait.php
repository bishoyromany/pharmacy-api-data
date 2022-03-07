<?php

namespace App\Traits\Sync;

use GuzzleHttp\Client;
use App\Models\Pharmacy;
use App\Models\Drug;
use App\Models\ActiveDrugs;
use App\Models\RX;

trait DrugsSyncTrait
{
    public function drugs(Pharmacy $pharmacy)
    {
        $client = new Client;
        $page = 1;
        $perpage = 10000;
        $total = $page * 20000;

        while ($page * $perpage <= $total + $perpage) {
            $response = $client->get($pharmacy->config->api . "/DRUG", [
                'query' => [
                    'perpage' => $perpage,
                    'page' => $page,
                    'password' => $pharmacy->config->api_password,
                    // 'where' => 'DRGNDC',
                    // 'value' => '80777027399'
                ]
            ]);

            $data = json_decode($response->getBody()->getcontents());
            $pagination = $data->pagination;
            $data = $data->data;

            $page = $pagination->nextPage;
            $perpage = $pagination->perpage;
            $total = $pagination->total;

            collect($data)->map(function ($item) use ($pharmacy) {
                $storeData = [];
                foreach ($this->drugsColumns as $column) {
                    $storeData[$column] = $item->{$column};
                }
                $storeData['pharmacy_id'] = $pharmacy->id;
                $storeData['user_id'] = $pharmacy->user_id;
                $storeData['data'] = $item;
                if (isset($storeData['CREATIONDATE'])) {
                    $storeData['created_at'] = $storeData['CREATIONDATE'];
                    $storeData['updated_at'] = $storeData['CREATIONDATE'];
                }

                Drug::updateOrCreate(
                    ['DRGNDC' => $storeData['DRGNDC'], 'pharmacy_id' => $pharmacy->id],
                    $storeData
                );
            });
        }
    }

    public function activeDrugs(Pharmacy $pharmacy)
    {
        $client = new Client;
        $page = 1;
        $perpage = 200;
        $ndcs = [[]];
        $index = 0;
        RX::where('pharmacy_id', '=', $pharmacy->id)
            ->select('NDC')->distinct('NDC')->get()->map(function ($item) use (&$ndcs, &$index, $perpage) {
                if (count($ndcs[$index]) < $perpage) {
                    $ndcs[$index][] = $item->NDC;
                } else {
                    $index++;
                    $ndcs[$index][] = $item->NDC;
                }
            });

        foreach ($ndcs as $ndc) {
            $response = $client->get($pharmacy->config->api . "/DRUG", [
                'query' => [
                    'perpage' => $perpage,
                    'page' => $page,
                    'password' => $pharmacy->config->api_password,
                    'where' => 'DRGNDC',
                    'operator' => 'in',
                    'value' => implode(',', $ndc)
                ]
            ]);

            $data = json_decode($response->getBody()->getcontents());
            $data = $data->data;

            foreach ($data as $item) {
                $item = (array)$item;
                $storeData = [];
                foreach ($this->activeDrugsColumns as $column) {
                    $storeData[$column] = $item[$column];
                }
                $storeData['pharmacy_id'] = $pharmacy->id;
                $storeData['user_id'] = $pharmacy->user_id;
                $storeData['NDC'] = $item['DRGNDC'];
                $storeData['pack'] = $item['QNTPACK'];
                $storeData['stock'] = $item['QNTHAND'];
                $storeData['type'] = $item['DRGTYPE'];
                $storeData['form'] = $item['FORM'];
                $storeData['data'] = $item;
                if (isset($storeData['CREATIONDATE'])) {
                    $storeData['created_at'] = $storeData['CREATIONDATE'];
                    $storeData['updated_at'] = $storeData['LDISP'] ?? $storeData['CREATIONDATE'];
                }
                activeDrugs::updateOrCreate(
                    ['NDC' => $storeData['NDC'], 'pharmacy_id' => $pharmacy->id],
                    $storeData
                );
            }
        }
    }
}
