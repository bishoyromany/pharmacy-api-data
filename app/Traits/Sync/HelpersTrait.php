<?php

namespace App\Traits\Sync;
use Illuminate\Support\Facades\Cache;
trait HelpersTrait
{
    public static function sendData(string $table, array $data, string $cacheColumn){
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            env("API_URL") . "/pharmacy/sync/data",
            [
                'form_params' => [
                    'table' => $table,
                    'pharmacy' => env("PHARMACY_USERNAME"),
                    'password' => env("API_PASSWORD"),
                    'data' => $data,
                ]
            ]
        )->getBody()->getContents();

        Cache::put($table, $data[count($data) - 1]->$cacheColumn, now()->addMinutes(30));

        return $response;
    }
}
