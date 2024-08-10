<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\V1\WilayahController;
use App\Http\Controllers\Api\TrackMobileController;
use App\Http\Controllers\Api\TrackOpendkController;
use App\Http\Controllers\Api\TrackOpenkabController;

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
    ->middleware(['auth:sanctum', 'ability:pantau-wilayah'])
    ->group(function () {
        Route::get('/', [WilayahController::class, 'index']);
        Route::get('provinsi', [WilayahController::class, 'provinsi']);
        Route::get('kabupaten', [WilayahController::class, 'kabupaten']);
        Route::get('kecamatan', [WilayahController::class, 'kecamatan']);
        Route::get('desa', [WilayahController::class, 'desa']);
    });

Route::prefix('track')
    ->middleware(['auth:sanctum', 'ability:pantau-track'])
    ->group(function () {
        Route::post('desa', TrackController::class);
        Route::post('opendk', TrackOpendkController::class);
        Route::get('mobile', TrackMobileController::class);
        Route::post('openkab', TrackOpenkabController::class);
    });
