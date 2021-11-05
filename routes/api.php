<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DynamicTableController;
use App\Http\Controllers\DynamicPOSSTableController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware(["App\Http\Middleware\DevelopmentMiddleware"])->prefix("v1.0")->group(function(){
    Route::get("patients", [PatientController::class, "index"]);
    Route::get("dynamic/{table}", [DynamicTableController::class, "index"]);
    Route::get("dynamic/poss/{table}", [DynamicPOSSTableController::class, "index"]);
});