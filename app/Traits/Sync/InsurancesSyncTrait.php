<?php

namespace App\Traits\Sync;

use App\Models\Pharmacy;
use App\Models\Insurance;
use App\Models\RXPay;
use App\Models\RX;

trait InsurancesSyncTrait
{
    public function insurances(Pharmacy $pharmacy)
    {
        RXPay::where('pharmacy_id', '=', $pharmacy->id)
            ->select('INS_CODE')->distinct('INS_CODE')->get()->map(function ($item) use ($pharmacy) {
                $insuranceCode = $item->INS_CODE;
                $copay = 0;
                $paid = 0;

                RXPay::where('pharmacy_id', '=', $pharmacy->id)->select('RXNO')->distinct('RXNO')->get()->map(function ($item) use ($pharmacy, &$copay, &$paid, $insuranceCode) {
                    $rx = RX::where('pharmacy_id', '=', $pharmacy->id)->where('RXNO', '=', $item->RXNO)->where('STATUS', '=', 'B')->first();
                    if (!$rx) {
                        return false;
                    }
                    $amount = RXPay::getInsurancePaid($pharmacy->id, $rx, $insuranceCode);
                    $copay += $amount['copay'];
                    $paid += $amount['insurance'];
                });

                $storeData = [
                    'code' => $insuranceCode,
                    'name' => $insuranceCode,
                    'user_id' => $pharmacy->user_id,
                    'pharmacy_id' => $pharmacy->id,
                    'data' => [],
                    'copay' => $copay,
                    'paid' => $paid
                ];

                Insurance::updateOrCreate(
                    ['code' => $insuranceCode, 'pharmacy_id' => $pharmacy->id],
                    $storeData
                );
            });
    }
}
