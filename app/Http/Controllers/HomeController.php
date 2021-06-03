<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        $users = DB::select("SELECT TOP 10 * FROM PATINS");
        dd($users);
    }
}
