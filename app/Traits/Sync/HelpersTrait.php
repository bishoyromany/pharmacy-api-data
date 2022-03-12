<?php

namespace App\Traits\Sync;
use Illuminate\Support\Facades\Cache;
use Guzzle\Http\Exception\ClientErrorResponseException;

trait HelpersTrait
{
    public static function sendData(string $table, array $data, string $cacheColumn){
        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->post(
                env("API_URL") . "/pharmacy/sync/data",
                [
                    'json' => [
                        'table' => $table,
                        'pharmacy' => env("PHARMACY_USERNAME"),
                        'password' => env("API_PASSWORD"),
                        'data' => $data,
                    ]
                ]
            )->getBody()->getContents();
            
            if(count($data) > 0){
                Cache::put($table, $data[count($data) - 1][$cacheColumn], now()->addMinutes(60));
            }
        }catch(\GuzzleHttp\Exception\RequestException $e){
            try{
                $response = $e->getResponse()->getBody()->getContents();
            }catch(\Exception $e){
                $response = $e->getMessage();
            }
        }

        return $response;
    }
}
