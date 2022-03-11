<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PharmacySyncController;

class HomeController extends Controller
{
    public function index(){
        return response((new PharmacySyncController)->indeX());
       dd(\Artisan::call('pharmacy:sync'));
    }

    public function cache(){
        $caches = ["transactions"];
        $result = [];
        foreach($caches as $cache){
            $result = [
                'key' => $cache,
                'value' => cache()->get($cache)
            ];
        }
        dd($result);
    }
}
