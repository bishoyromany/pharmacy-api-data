<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DynamicPOSSTable;
use App\Http\Traits\Helpers;

class TransactionController extends Controller
{
    use Helpers;

    protected $tables = [
        "trasnactions" => 'POSTransaction',
        "paymentTypes" => 'PayType',
        "paymentDetails" => 'POSTransPayment'
    ];

    public function __construct(){
        $this->table = app(DynamicPOSSTable::class);
    }

    public function index(Request $request){
        $table = $this->tables['trasnactions'];
        $this->table->setTable($table);
        $data = $this->pagination($request);
        $data['data']->map(function($item){
            $paymentDetails = app(DynamicPOSSTable::class)->setTable($this->tables['paymentDetails'])->where('TransID', $item->TransID)->first();
            // $item->paymentDetails = $paymentDetails;
            $paymentTypes = app(DynamicPOSSTable::class)->setTable($this->tables['paymentTypes'])->where('PayTypeID', $paymentDetails->TransTypeCode)->first();
            $item->paymentTypes = $paymentTypes;
            return $item;
        });
        // $this->table = $this->table->join($this->tables['paymentDetails'], $this->tables['trasnactions'].'.TransID', '=', $this->tables['paymentDetails'].'.TransID');
        // $this->table = $this->table->join($this->tables['paymentTypes'], $this->tables['paymentDetails'].'.TransTypeCode', '=', $this->tables['paymentTypes'].'.PayTypeID');
        if(isset($request->json) && $request->json === false){
            return $data;
        }
        return $this->api($data);
    }
}
