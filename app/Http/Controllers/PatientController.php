<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Http\Traits\Helpers;

class PatientController extends Controller
{
    use Helpers;

    protected $table;

    public function __construct(){
        $this->table = app(Patient::class);
    }

    public function index(Request $request){
        return $this->api($this->pagination($request));
    }
}
