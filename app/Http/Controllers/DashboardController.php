<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\PengaturanAplikasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    /** @var Desa */
    protected $desa;

    public function __construct()
    {
        $this->desa = new Desa();
    }

    public function index()
    {
        $pengaturanAplikasi = PengaturanAplikasi::get_pengaturan();
        $pengaturanAplikasi['akhir_backup'] = !empty($pengaturanAplikasi['akhir_backup']) ? $pengaturanAplikasi['akhir_backup'] : Carbon::now()->startOfMonth()->format('Y-m-d');
        return view('dashboard', [
            'jumlahDesa' => $this->desa->jumlahDesa()->get()->first(),
            'desaBaru' => $this->desa->desaBaru()->count(),
            'kabupatenKosong' => collect($this->desa->kabupatenKosong())->count(),
            'info_backup' => [
                'cloud_storage' => $pengaturanAplikasi['cloud_storage'],
                'akhir_backup' => $pengaturanAplikasi['akhir_backup'],
                'waktu_backup' => $pengaturanAplikasi['waktu_backup'],
                'info' => 'Peringatan !!!',
                'isi' => 'Gagal Backup Otomatis ke Cloud Storage pada tanggal '.Carbon::createFromFormat('Y-m-d', $pengaturanAplikasi['akhir_backup'])->addDays($pengaturanAplikasi['waktu_backup'])->format('Y-m-d'),
            ],
        ]);
    }

    public function datatableDesaBaru(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of($this->desa->desaBaru()->get()->map(function ($desa) {
                if (auth()->check() == false) {
                    unset($desa['url_hosting']);
                }

                return $desa;
            }))->addIndexColumn()->make(true);
        }

        abort(404);
    }

    public function datatableKabupatenKosong(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of($this->desa->kabupatenKosong())->addIndexColumn()->make(true);
        }

        abort(404);
    }
}
