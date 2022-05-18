<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Sync\HelpersTrait;
use App\Models\RX;

class SyncPrefillRXController extends Controller
{
    public function index()
    {
        $result = [
            'exists' => 0,
            'created' => 0
        ];
        $page = 1;
        while ($page) {
            try {
                $data = HelpersTrait::sendGetRequest("/pharmacy/rx/prefill", []);
                $page = $data['pagination']['nextPage'] ?? null;
                foreach ($data['data'] as $rx) {
                    $rxData = $rx['data'];
                    if (isset($rxData['row_num'])) {
                        unset($rxData['row_num']);
                    }
                    $exists = RX::where('RXNO', '=', $rxData['RXNO'])->where('NREFILL', '=', $rxData['NREFILL'])->first();
                    if (!$exists) {
                        RX::create($rxData);
                        $result['created']++;
                    } else {
                        $result['exists']++;
                    }
                }
            } catch (\Exception $e) {
                $page = null;
                HelpersTrait::log("Finished Prefill RX Data Sync Error", false, json_encode(['response' => $result, 'error' => $e->getMessage()]));
                return json_encode($result);
            }
        }

        HelpersTrait::log("Finished Prefill RX Data Sync", true, json_encode(['response' => $result]));

        return json_encode($result);
    }
}
