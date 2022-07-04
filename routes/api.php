<?php

use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\WilayahController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('wilayah')
    ->middleware('tracksid')
    ->group(function () {
        Route::get('desa', [WilayahController::class, 'desa']);
        Route::get('caridesa', [WilayahController::class, 'cariDesa']);
        Route::get('ambildesa', [WilayahController::class, 'ambilDesa']);
        Route::get('kodedesa', [WilayahController::class, 'kodeDesa']);
        Route::get('list_wilayah', [WilayahController::class, 'listWilayah']);
    });

Route::prefix('track')
    ->middleware('tracksid')
    ->group(function () {
        Route::post('desa', TrackController::class);
    });
