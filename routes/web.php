<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\AksesController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\Wilayah\DesaController;
use App\Http\Controllers\Admin\Wilayah\ProvinsiController;
use App\Http\Controllers\Admin\Wilayah\KabupatenController;
use App\Http\Controllers\Admin\Wilayah\KecamatanController;

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
        Route::get('/', [PetaController::class, 'index']);
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

// akses
Route::prefix('akses')
    ->middleware('auth')
    ->group(function () {
        Route::get('bersihkan', AksesController::class);
    });

// notifikasi
Route::middleware('auth')
    ->group(function () {
        Route::resource('notifikasi', NotifikasiController::class);
        Route::get('notifikasi/edit/{id}', [NotifikasiController::class, 'edit'])->name('notifikasi.edit');
        Route::post('notifikasi/update/{id}', [NotifikasiController::class, 'update'])->name('notifikasi.update');
    });


Route::prefix('profile')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::post('update', [ProfileController::class, 'update']);
        Route::get('reset-password', [ProfileController::class, 'resetPassword']);
        Route::post('reset-password', [ProfileController::class, 'resetPasswordUpdate']);
    });

Route::middleware('auth')
    ->group(function () {
        Route::resource('akun-pengguna', PenggunaController::class);
        Route::get('akun-pengguna/datatables', [PenggunaController::class, 'show'])->name('akun-pengguna.datatables');
    });

// Wilayah Provinsi
Route::prefix('provinsi')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [ProvinsiController::class, 'index']);
        Route::get('/datatables', [ProvinsiController::class, 'datatables'])->name('provinsi.datatables');
        ;
    });

// Wilayah Kabupaten
Route::prefix('kabupaten')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [KabupatenController::class, 'index']);
        Route::get('/datatables', [KabupatenController::class, 'datatables'])->name('kabupaten.datatables');
    });

// Wilayah Kecamatan
Route::prefix('kecamatan')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [KecamatanController::class, 'index']);
        Route::get('/datatables', [KecamatanController::class, 'datatables'])->name('kecamatan.datatables');
    });

// Wilayah Desa / Keluarahan
Route::resource('desa', '\App\Http\Controllers\Admin\Wilayah\DesaController', ['except' => ['show']])->middleware('auth');
Route::get('desa/import', [DesaController::class, 'import'])->name('desa.import')->middleware('auth');
Route::post('desa/proses-import', [DesaController::class, 'prosesImport'])->name('desa.proses-import')->middleware('auth');
