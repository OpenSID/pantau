<?php

use App\Http\Controllers\Api\InstallOpenDKController;
use App\Http\Controllers\Api\InstallOpensidController;
use App\Http\Controllers\Api\MargaController;
use App\Http\Controllers\Api\SukuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Api\TrackMobileController;
use App\Http\Controllers\Api\TrackOpendkController;
use App\Http\Controllers\Api\TrackOpenkabController;
use App\Http\Controllers\WebsiteDashboardController;
use App\Http\Controllers\Api\TrackKelolaDesaController;
use App\Http\Controllers\KelolaDesaDashboardController;
use App\Http\Controllers\LayananDesaDashboardController;
use App\Http\Controllers\Api\TrackPBBController;

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
        Route::get('region', [WilayahController::class, 'regionData']);
        Route::get('desa', [WilayahController::class, 'desa']);
        Route::get('caridesa', [WilayahController::class, 'cariDesa']);
        Route::get('carikabupaten', [WilayahController::class, 'cariKabupaten']);
        Route::get('ambildesa', [WilayahController::class, 'ambilDesa']);
        Route::get('kodedesa', [WilayahController::class, 'kodeDesa']);
        Route::get('kodekecamatan', [WilayahController::class, 'kodeKecamatan']);
        Route::get('list_wilayah', [WilayahController::class, 'listWilayah']);
        Route::get('kabupaten-desa', [WilayahController::class, 'kabupatenDesa']);
        Route::get('suku', [SukuController::class, 'index']);
        Route::get('marga', [MargaController::class, 'index']);
    });

Route::prefix('track')
    ->middleware('tracksid')
    ->group(function () {
        Route::post('desa', TrackController::class);
        Route::post('opendk', TrackOpendkController::class);
        Route::post('openkab', TrackOpenkabController::class);
        Route::post('mobile', TrackMobileController::class);
        Route::post('keloladesa', [TrackKelolaDesaController::class, 'store']);
        Route::post('pbb', TrackPBBController::class);
    });

Route::prefix('web')
    ->group(function () {
        Route::get('chart-usage/{data?}', [WebsiteDashboardController::class, 'chartUsage']);
        Route::get('summary', [WebsiteDashboardController::class, 'summary']);
        Route::get('summary-keloladesa', [KelolaDesaDashboardController::class, 'summary']);
        Route::get('summary-layanan', [LayananDesaDashboardController::class, 'summary']);
        Route::get('chart-opensid', [InstallOpensidController::class, 'chart']);
        Route::get('chart-opendk', [InstallOpenDKController::class, 'chart']);
    });
