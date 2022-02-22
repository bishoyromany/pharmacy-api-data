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
        return $this->api($this->pagination($request));
    }
}
