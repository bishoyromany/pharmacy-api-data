<?php

namespace App\Traits\Sync;

use Illuminate\Support\Facades\Cache;
use Guzzle\Http\Exception\ClientErrorResponseException;

trait HelpersTrait
{
    public static function sendData(string $table, array $data, string $cacheColumn)
    {
        $client = new \GuzzleHttp\Client();
        try {
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

            if (count($data) > 0) {
                Cache::put($table, $data[count($data) - 1][$cacheColumn], now()->addDay());
            }
            \Log::info("Success Data Sync For " . $table, ['cache' => cache()->get($table), 'response' => $response]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            try {
                $response = $e->getResponse()->getBody()->getContents();
                \Log::info("Failed Data Sync For " . $table, ['cache' => cache()->get($table), 'response' => $response]);
            } catch (\Exception $e) {
                $response = $e->getMessage();
                \Log::error("Failed Data Sync For " . $table, ['cache' => cache()->get($table), 'error' => $e->getMessage()]);
            }
        }

        return $response;
    }

    /**
     * Send Get Request
     */
    public static function sendGetRequest(string $url, array $params = [])
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get(
                env("API_URL") . $url,
                [
                    'params' => [
                        'pharmacy' => env("PHARMACY_USERNAME"),
                        'password' => env("API_PASSWORD"),
                        'params' => $params
                    ]
                ]
            )->getBody()->getContents();
            dd($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            try {
                $response = $e->getResponse()->getBody()->getContents();
                \Log::info("Failed Get Request To " . $url, ['response' => $response]);
            } catch (\Exception $e) {
                $response = $e->getMessage();
                \Log::error("Failed Get Request To " . $url, ['error' => $e->getMessage()]);
            }
        }

        return $response;
    }

    public static function log(string $message, bool $status, string $description)
    {
        $client = new \GuzzleHttp\Client();
        if ($status) {
            \Log::info($message, ['description' => $description]);
        } else {
            \Log::error($message, ['description' => $description]);
        }
        try {
            $response = $client->post(
                env("API_URL") . "/pharmacy/sync/data/log",
                [
                    'json' => [
                        'pharmacy' => env("PHARMACY_USERNAME"),
                        'password' => env("API_PASSWORD"),
                        'message' => $message,
                        'description' => $description,
                        'status' => $status,
                    ]
                ]
            )->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            try {
                $response = $e->getResponse()->getBody()->getContents();
                \Log::info("Failed To Add Cache", ['response' => $response]);
            } catch (\Exception $e) {
                $response = $e->getMessage();
                \Log::error("Failed To Add Cache", ['error' => $e->getMessage()]);
            }
        }

        return $response;
    }

    public static function getLatestSyncTime(string $table)
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get(
                env("API_URL") . "/pharmacy/sync/data/last/record",
                [
                    'query' => [
                        'table' => $table,
                        'pharmacy' => env("PHARMACY_USERNAME"),
                        'password' => env("API_PASSWORD"),
                    ]
                ]
            )->getBody()->getContents();
            self::log("Success Get Latest Sync Time For " . $table, true, $response);

            $response = json_decode($response);
            return $response->data->date ?? null;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            try {
                $response = $e->getResponse()->getBody()->getContents();
                self::log("Failed Get Latest Sync Time For " . $table, false, $response);
            } catch (\Exception $e) {
                $response = $e->getMessage();
                self::log("Failed Get Latest Sync Time For " . $table, false, $e->getMessage());
            }
        }

        return $response;
    }
}
