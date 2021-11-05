<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicPOSSTable;
use App\Http\Traits\Helpers;
class DynamicPOSSTableController extends Controller
{
    use Helpers;

    public function __construct(){
        $this->table = app(DynamicPOSSTable::class);
    }

    public function index(String $table, Request $request){
        $this->table->setTable($table);
        return $this->api($this->pagination($request));
    }
}
