<?php

use App\Http\Controllers\DashboardController;
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

// laporan
Route::prefix('laporan')
    ->group(function () {
        // TODO: laporan
    });

Route::prefix('review')
    ->middleware('auth')
    ->group(function () {
        // TODO: review
    });