<?php

namespace App\Traits\Sync;
use Illuminate\Support\Facades\Cache;
trait HelpersTrait
{
    public static function sendData(string $table, array $data, string $cacheColumn){
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            env("API_URL"),
            [
                'form_params' => [
                    'table' => $table,
                    'data' => $data,
                    'pharmacy' => env("PHARMACY_USERNAME"),
                    'password' => env("API_PASSWORD")
                ]
            ]
        );

        Cache::put($table, $data[count($data) - 1]->$cacheColumn);

        return $response;
    }
}
