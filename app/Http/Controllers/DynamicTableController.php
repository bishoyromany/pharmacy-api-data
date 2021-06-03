<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicTable;
use App\Http\Traits\Helpers;

class DynamicTableController extends Controller
{
    use Helpers;

    public function __construct(){
        $this->table = app(DynamicTable::class);
    }

    public function index(String $table, Request $request){
        $this->table->setTable($table);
        return $this->api($this->pagination($request));
    }
}
