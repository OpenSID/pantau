<?php

use App\Http\Controllers\Admin\AdatController;
use App\Http\Controllers\Admin\MargaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PbbController;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\AksesController;
use App\Http\Middleware\PantauMiddleware;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\OpendkController;
use App\Http\Controllers\PantauController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\WilayahMiddleware;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OpenkabController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanTemaController;
use App\Http\Controllers\OpenDKDashboardController;
use App\Http\Controllers\LaporanDesaAktifController;
use App\Http\Controllers\OpenKabDashboardController;
use App\Http\Controllers\WebsiteDashboardController;
use App\Http\Controllers\Admin\Wilayah\DesaController;
use App\Http\Controllers\KelolaDesaDashboardController;
use App\Http\Controllers\LayananDesaDashboardController;
use App\Http\Controllers\Admin\Wilayah\ProvinsiController;
use App\Http\Controllers\Admin\Wilayah\KabupatenController;
use App\Http\Controllers\Admin\Wilayah\KecamatanController;
use App\Http\Controllers\Admin\Pengaturan\PengaturanAplikasiController;
use App\Http\Controllers\Admin\SukuController;
use App\Http\Controllers\PetaPeriodController;

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

Route::group(['middleware' => 'web.dashboard'],function(){
    Route::get('/', [WebsiteDashboardController::class, 'index']);
    Route::prefix('web')->group(function(){
        Route::get('', [WebsiteDashboardController::class, 'index']);
        Route::get('openkab', [WebsiteDashboardController::class, 'openkab']);
        Route::get('openkab/peta', [OpenKabDashboardController::class, 'peta']);
        Route::get('opensid', [WebsiteDashboardController::class, 'opensid']);
        Route::get('opensid/versi', [WebsiteDashboardController::class, 'opensid_versi']);
        Route::get('opensid/versi/detail', [WebsiteDashboardController::class, 'opensid_versi_detail']);
        Route::get('opensid/peta', [PetaPeriodController::class, 'index']);
        Route::get('keloladesa', [WebsiteDashboardController::class, 'keloladesa']);
        Route::get('opensid-data', [WebsiteDashboardController::class, 'opensidData']);
        Route::get('pbb-data', [WebsiteDashboardController::class, 'pbbData']);
        Route::get('openkab-data', [WebsiteDashboardController::class, 'openkabData']);
        Route::get('layanandesa', [LayananDesaDashboardController::class, 'index']);
        Route::get('layanandesa/detail', [LayananDesaDashboardController::class, 'detail']);
        Route::get('layanandesa/versi', [LayananDesaDashboardController::class, 'versi']);
        Route::get('layanandesa/versi/detail', [LayananDesaDashboardController::class, 'versi_detail']);
        Route::get('layanandesa/install_baru', [LayananDesaDashboardController::class, 'install_baru']);
        Route::get('layanandesa/peta', [LayananDesaDashboardController::class, 'peta']);
        Route::get('keloladesa', [KelolaDesaDashboardController::class, 'index']);
        Route::get('keloladesa/detail', [KelolaDesaDashboardController::class, 'detail']);
        Route::get('keloladesa/versi', [KelolaDesaDashboardController::class, 'versi']);
        Route::get('keloladesa/versi/detail', [KelolaDesaDashboardController::class, 'versi_detail']);
        Route::get('keloladesa/install_baru', [KelolaDesaDashboardController::class, 'install_baru']);
        Route::get('keloladesa/peta', [KelolaDesaDashboardController::class, 'peta']);
        Route::get('data-peta', [DashboardController::class, 'dataPeta']);
        Route::get('opendk', [OpenDKDashboardController::class, 'index']);
        Route::get('opendk/detail', [OpenDKDashboardController::class, 'detail'])->name('web.opendk.detail');
        Route::get('opendk/versi', [OpenDKDashboardController::class, 'versi']);
        Route::get('opendk/versi/detail', [OpenDKDashboardController::class, 'versi_detail']);
        Route::get('opendk/install_baru', [OpenDKDashboardController::class, 'install_baru']);
        Route::get('opendk/peta', [OpenDKDashboardController::class, 'peta']);
    });
});

// datatable
Route::prefix('datatables')->as('datatables:')
    ->group(function () {
        Route::get('desa-baru', [DashboardController::class, 'datatableDesaBaru'])->name('desa-baru');
        Route::get('kabupaten-kosong', [DashboardController::class, 'datatableKabupatenKosong'])->name('kabupaten-kosong');
        Route::get('opendk-baru', [DashboardController::class, 'datatableOpendkBaru'])->name('opendk-baru');
        Route::get('openkab-baru', [DashboardController::class, 'datatableOpenkabBaru'])->name('openkab-baru');
        Route::get('opensid-baru', [DashboardController::class, 'datatableOpensidBaru'])->name('opensid-baru');
        Route::get('layanandesa-baru', [DashboardController::class, 'datatableLayanandesaBaru'])->name('layanandesa-baru');
        Route::get('keloladesa-baru', [DashboardController::class, 'datatableKeloladesaBaru'])->name('keloladesa-baru');
        Route::get('pbb-baru', [DashboardController::class, 'datatablePbbBaru'])->name('pbb-baru');
        Route::get('pengguna-keloladesa', [DashboardController::class, 'datatablePenggunaKeloladesa'])->name('pengguna-keloladesa');
        Route::get('pengguna-layanandesa', [DashboardController::class, 'datatablePenggunaLayanandesa'])->name('pengguna-layanandesa');
        Route::get('pengguna-opendk', [DashboardController::class, 'datatablePenggunaOpendk'])->name('pengguna-opendk');
        Route::get('pengguna-openkab', [DashboardController::class, 'datatablePenggunaOpenkab'])->name('pengguna-openkab');
        Route::get('pengguna-opensid', [DashboardController::class, 'datatablePenggunaOpensid'])->name('pengguna-opensid');
        Route::get('pengguna-pbb', [DashboardController::class, 'datatablePenggunaPbb'])->name('pengguna-pbb');
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
        Route::get('desa-aktif', [LaporanDesaAktifController::class, 'index']);
        Route::get('tema', [LaporanTemaController::class, 'index']);
    });

// PBB
Route::prefix('pbb')
    ->group(function () {
        Route::get('kecamatan', [PbbController::class, 'kecamatan']);
        Route::get('desa', [PbbController::class, 'kecamatan']);
        Route::delete('desa/{desa}', [PbbController::class, 'deleteDesa'])->middleware('auth');
        Route::get('kabupaten', [PbbController::class, 'kabupaten']);
        Route::get('versi', [PbbController::class, 'versi']);
    });

Route::prefix('mobile')
    ->group(function () {
        Route::get('/', [MobileController::class, 'index']);
        Route::get('pengguna', [MobileController::class, 'pengguna']);
        Route::get('pengguna_kelola_desa', [MobileController::class, 'penggunaKelolaDesa']);
        Route::get('desa', [MobileController::class, 'desa']);
    });

//opendk
Route::prefix('opendk') ->group(function () {
    Route::get('/', [OpendkController::class, 'index']);
    Route::get('versi', [OpendkController::class, 'versi']);
    Route::get('kecamatan', [OpendkController::class, 'kecamatan']);
    Route::get('kabupaten', [OpendkController::class, 'kabupaten']);
    Route::get('peta', [OpendkController::class, 'peta']);
    Route::get('kabupaten-kosong', [OpendkController::class, 'kabupatenkosong'])->name('opendk.kabupatenkosong');
});

//openkab
Route::prefix('openkab') ->group(function () {
    Route::get('/kerja-sama', [OpenkabController::class, 'kerjaSama'])->name('openkab.kerjasama');
    Route::get('/get-wilayah', [OpenkabController::class, 'getWilayah'])->name('openkab.getwilayah');
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

    Route::resource('suku', SukuController::class)->except('show');
    Route::get('suku/import', [SukuController::class, 'import'])->name('suku.import');
    Route::post('suku/proses-import', [SukuController::class, 'prosesImport'])->name('suku.proses-import');
    Route::get('suku/contoh-import', [SukuController::class, 'contohImport'])->name('suku.contoh-import');

    Route::resource('marga', MargaController::class)->except('show');
    Route::get('marga/import', [MargaController::class, 'import'])->name('marga.import');
    Route::post('marga/proses-import', [MargaController::class, 'prosesImport'])->name('marga.proses-import');
    Route::get('marga/contoh-import', [MargaController::class, 'contohImport'])->name('marga.contoh-import');

    Route::resource('adat', AdatController::class)->except('show');
    Route::get('adat/import', [AdatController::class, 'import'])->name('adat.import');
    Route::post('adat/proses-import', [AdatController::class, 'prosesImport'])->name('adat.proses-import');
    Route::get('adat/contoh-import', [AdatController::class, 'contohImport'])->name('adat.contoh-import');

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
