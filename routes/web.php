<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Test\LatestDataSyncController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('/test', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/test/cache', [App\Http\Controllers\HomeController::class, 'cache']);
Route::get('/test/latest/data/sync/time', [LatestDataSyncController::class, 'getLatestTime']);
