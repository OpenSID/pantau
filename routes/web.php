<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\AksesController;
use App\Http\Middleware\PantauMiddleware;
use App\Http\Controllers\OpendkController;
use App\Http\Controllers\PantauController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\WilayahMiddleware;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\Wilayah\DesaController;
use App\Http\Controllers\Admin\Wilayah\ProvinsiController;
use App\Http\Controllers\Admin\Wilayah\KabupatenController;
use App\Http\Controllers\Admin\Wilayah\KecamatanController;
use App\Http\Controllers\Admin\Pengaturan\PengaturanAplikasiController;
use App\Http\Controllers\MobileController;

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
Route::get('/', [DashboardController::class, 'index']);

// datatable
Route::prefix('datatables')->as('datatables:')
    ->group(function () {
        Route::get('desa-baru', [DashboardController::class, 'datatableDesaBaru'])->name('desa-baru');
        Route::get('kabupaten-kosong', [DashboardController::class, 'datatableKabupatenKosong'])->name('kabupaten-kosong');
    });

// Peta
Route::get('peta', [PetaController::class, 'index']);

// Sesi Provinsi
Route::prefix('sesi')
    ->group(function () {
        Route::middleware(WilayahMiddleware::class)->get('provinsi/{provinsi}', function () {
            return redirect('/');
        });
        Route::get('hapus', function () {
            session()->remove('provinsi');

            return redirect('/');
        });
        Route::get('hapus-pantau', function () {
            session()->remove('pantau');

            return redirect('/');
        });

        Route::middleware(PantauMiddleware::class)->get('pantau/{pantau}', function (Request $request)
        {
            return redirect($request->pantau?? '/');
        });
    });

// Laporan
Route::prefix('laporan')
    ->group(function () {
        Route::get('desa', [LaporanController::class, 'desa']);
        Route::delete('desa/{desa}', [LaporanController::class, 'deleteDesa'])->middleware('auth');
        Route::get('kabupaten', [LaporanController::class, 'kabupaten']);
        Route::get('versi', [LaporanController::class, 'versi']);
    });

Route::prefix('mobile')
    ->group(function () {
        Route::get('/', [MobileController::class, 'index']);
        Route::get('pengguna', [MobileController::class, 'pengguna']);
        Route::get('desa', [MobileController::class, 'desa']);
    });

//opendk
Route::prefix('opendk') ->group(function () {
    Route::get('/', [OpendkController::class, 'index']);
    Route::get('versi', [OpendkController::class, 'versi']);
    Route::get('kecamatan', [OpendkController::class, 'kecamatan']);
    Route::get('kabupaten', [OpendkController::class, 'kabupaten']);
    Route::get('peta', [OpendkController::class, 'peta']);
    Route::get('kabupaten-kosong', [OpendkController::class, 'kabupatenkosong'])->name('opendk.kabupatenkosong');;
});
// Wilayah
Route::get('wilayah', WilayahController::class);

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Riview
    Route::prefix('review')->group(function () {
        Route::get('desa-baru', [ReviewController::class, 'desaBaru']);
        Route::get('non-aktif', [ReviewController::class, 'nonAktif']);
    });

    // Akses
    Route::get('akses/bersihkan', AksesController::class);

    // Wilayah Provinsi
    Route::prefix('provinsi')->group(function () {
        Route::get('/', [ProvinsiController::class, 'index']);
        Route::get('/datatables', [ProvinsiController::class, 'datatables'])->name('provinsi.datatables');
    });

    // Wilayah Kabupaten
    Route::prefix('kabupaten')->group(function () {
        Route::get('/', [KabupatenController::class, 'index']);
        Route::get('/datatables', [KabupatenController::class, 'datatables'])->name('kabupaten.datatables');
    });

    // Wilayah Kecamatan
    Route::resource('kecamatan', KecamatanController::class)->except('show');
    // Route::prefix('kecamatan')->group(function () {

    //     Route::get('/datatables', [KecamatanController::class, 'datatables'])->name('kecamatan.datatables');
    // });


    // Wilayah Desa / Keluarahan
    Route::resource('desa', DesaController::class)->except('show');
    Route::get('desa/import', [DesaController::class, 'import'])->name('desa.import');
    Route::post('desa/proses-import', [DesaController::class, 'prosesImport'])->name('desa.proses-import');
    Route::get('desa/contoh-import', [DesaController::class, 'contohImport'])->name('desa.contoh-import');

    // Pengguna
    Route::resource('akun-pengguna', PenggunaController::class);
    Route::get('akun-pengguna/datatables', [PenggunaController::class, 'show'])->name('akun-pengguna.datatables');

    // Profil
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::post('update', [ProfileController::class, 'update']);
        Route::get('reset-password', [ProfileController::class, 'resetPassword']);
        Route::post('reset-password', [ProfileController::class, 'resetPasswordUpdate']);
    });

    // Profil
    Route::prefix('pengaturan')->group(function () {
        Route::get('/', [PengaturanAplikasiController::class, 'index']);
        Route::get('aplikasi', [PengaturanAplikasiController::class, 'index']);
        Route::post('aplikasi', [PengaturanAplikasiController::class, 'store'])->name('pengaturan.aplikasi.store');;
    });

});
