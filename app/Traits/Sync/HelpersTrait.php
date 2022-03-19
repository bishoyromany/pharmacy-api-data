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
            \Log::info("Success Data Sync For ".$table, ['cache' => cache()->get($table), 'response' => $response]);

        }catch(\GuzzleHttp\Exception\RequestException $e){
            try{
                $response = $e->getResponse()->getBody()->getContents();
                \Log::info("Failed Data Sync For ".$table, ['cache' => cache()->get($table), 'response' => $response]);
            }catch(\Exception $e){
                $response = $e->getMessage();
                \Log::error("Failed Data Sync For ".$table, ['cache' => cache()->get($table), 'error' => $e->getMessage()]);
            }
        }

        return $response;
    }
}
