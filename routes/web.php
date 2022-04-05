<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PenggunaController;
use App\Models\Desa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('test', function () {
    return (new Desa())->with('notifikasi')->paginate();
});

Auth::routes([
    'confirm' => true,
    'register' => false,
    'verify' => true,
]);

// index dashboard
Route::get('/', [DashboardController::class, 'index'])->middleware(['guest']);
Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// datatable
Route::prefix('datatables')->as('datatables:')
    ->group(function () {
        Route::get('desa-baru', [DashboardController::class, 'datatableDesaBaru'])->name('desa-baru');
        Route::get('kabupaten-kosong', [DashboardController::class, 'datatableKabupatenKosong'])->name('kabupaten-kosong');
    });

// peta
Route::prefix('peta')
    ->group(function () {
        Route::get('/', PetaController::class);
    });

// laporan
Route::prefix('laporan')
    ->group(function () {
        Route::get('desa', [LaporanController::class, 'desa']);
        Route::delete('desa/{desa}', [LaporanController::class, 'deleteDesa'])->middleware('auth');
        Route::get('kabupaten', [LaporanController::class, 'kabupaten']);
        Route::get('versi', [LaporanController::class, 'versi']);
    });

// wilayah
Route::prefix('wilayah')
    ->group(function () {
        Route::get('/', WilayahController::class);
    });

// review
Route::prefix('review')
    ->middleware('auth')
    ->group(function () {
        Route::get('desa-baru', [ReviewController::class, 'desaBaru']);
        Route::get('non-aktif', [ReviewController::class, 'nonAktif']);
    });

Route::middleware('auth')
    ->group(function () {
        Route::resource('akun-pengguna',  PenggunaController::class);
        Route::get('akun-pengguna/datatables', [PenggunaController::class, 'show'])->name('akun-pengguna.datatables');
    });
