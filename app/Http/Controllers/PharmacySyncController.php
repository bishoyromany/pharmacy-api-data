<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Sync\RXSyncTrait;
use App\Traits\Sync\DrugsSyncTrait;
use App\Traits\Sync\PatientsSyncTrait;
use App\Traits\Sync\InsurancesSyncTrait;
use App\Traits\Sync\TransactionsTrait;

class PharmacySyncController extends Controller
{
    use RXSyncTrait, DrugsSyncTrait, PatientsSyncTrait, InsurancesSyncTrait, TransactionsTrait;

    protected $patientsColumns = [
        'PATIENTNO', "PATTYPE", "MEDNO", "LNAME", "FNAME", "DOB", "SEX",
        "ADDRSTR", "ADDRCT", "ADDRST", "ADDRZP", "PHONE", "LANGUAGE", "EMAIL",
        "CREATIONDATE", "LASTMODIFIED"
    ];

    protected $rxVaccineColumns = [
        'RxNo', 'RefillNo', 'DoseNumber', 'NDC',
        'PatientNo', 'VISPresDate', 'VISPublishedDate', 'CompletionStatus', 'MissedVaccineAppointment',
        'VaccineRefusal', 'PatientConsent'
    ];

    protected $drugsColumns = [
        'NDC', 'DRGNAME', 'UPRICE', 'UPRICE_D', 'UPRICE_C'
    ];

    protected $activeDrugsColumns = [
        'DRGNDC', 'DRGNAME', 'UPRICE', 'UPRICE_C', 'STRONG'
    ];

    protected $rxPayColumns = [
        'RXNO', 'REFILL_NO', "FILLNO", "COVERAGECD", "ICOSTPAID", "TOTAMTPAID", "INSNETPAID", "AMT_COPINS", 'DATEPAID', 'INS_CODE'
    ];

    protected $rxColumns = [
        'PATIENTNO', 'RXNO', 'STATUS', 'NDC', 'DATEO', 'DATEF', 'QUANT', 'DAYS', 'AWP',
        'UnC', 'AMOUNT', 'PFEE', 'TOTAMT', 'BAL', 'UPRICE_C', 'TotBilledAmt', 'UPRICE', 'Cost', 'COPAY',
        'NREFILL', 'TREFILLS'
    ];

    protected $transactionsColumns = [
        'TransID', 'UserID', 'CustomerID', 'StationID', 'TransDate', 'TransType', 'GrossTotal', 'TotalDiscAmount', 'TotalTaxAmount',
        'TenderedAmount', 'TotalPaid'
    ];

    protected $all = false;

    protected $test = true;

    public function index($all = false, $resetRX = false)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $this->all = $all;
        $response = [];
        $response[] = $this->transactions();
        if($this->test){
            dd($response);
        }
        $this->patients();
        $this->rxPay( $resetRX);
        $this->rx($resetRX);
        $this->rxVaccine($resetRX);
        $this->activeDrugs();
        return response()->json([
            "success" => true,
            "result" => $response
        ]);
    }
}
