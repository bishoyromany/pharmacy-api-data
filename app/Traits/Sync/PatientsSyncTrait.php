<?php

namespace App\Traits\Sync;

use GuzzleHttp\Client;
use App\Models\Pharmacy;
use App\Models\Patient;

trait PatientsSyncTrait
{
    public function patients(Pharmacy $pharmacy)
    {
        $client = new Client;
        $page = 1;
        $perpage = 10000;
        $total = 20000;
        $latestRecord = Patient::where('pharmacy_id', $pharmacy->id)->orderBy('updated_at', 'desc')->select('updated_at')->first()->updated_at ?? null;

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
                $query['value'] = $latestRecord->format('Y-m-d');
                $query['where'] = 'LASTMODIFIED';
                $query['operator'] = ">=";
            }

            $response = $client->get($pharmacy->config->api . "/PATIENT", [
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
                foreach ($this->patientsColumns as $column) {
                    $storeData[$column] = $item->{$column};
                }
                $storeData['pharmacy_id'] = $pharmacy->id;
                $storeData['user_id'] = $pharmacy->user_id;
                $storeData['date'] = $storeData['DOB'] ?? "NOT_FOUND";
                $storeData['phoneNumber'] = $storeData['PHONE'];
                $storeData['name'] = $storeData['FNAME'] . " " . $storeData['LNAME'];
                $storeData['data'] = json_encode($item);
                $storeData['hash'] = md5(json_encode($storeData));
                $storeData['updated_at'] = $storeData['LASTMODIFIED'];
                $storeData['created_at'] = $storeData['CREATIONDATE'];

                Patient::updateOrCreate(
                    ['PATIENTNO' => $storeData['PATIENTNO'], 'pharmacy_id' => $pharmacy->id],
                    $storeData
                );
            });
        }
    }
}
