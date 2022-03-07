<?php

namespace App\Sync\Traits;

trait Helpers
{
    public static function sendData(string $table, array $data){
        $client = new \GuzzleHttp\Client();
        return $client->post(
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
    }
}
