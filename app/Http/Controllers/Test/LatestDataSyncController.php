<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Sync\HelpersTrait;

class LatestDataSyncController extends Controller
{
    use HelpersTrait;

    public function getLatestTime(Request $request){
        dd($this->getLatestSyncTime("rx"));
        dd($request->all());
    }
}
