<?php

namespace App\Http\Controllers\Admin\Pengaturan;

use Illuminate\Http\Request;
use App\Models\PengaturanAplikasi;
use App\Http\Controllers\Controller;
use Galahad\Aire\Support\Facades\Aire;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\PengaturanAplikasiRequest;

class PengaturanAplikasiController extends Controller
{
    public function index(Request $request)
    {
        $pengaturan = PengaturanAplikasi::where(['kategori' => 'setting'])->get();
        $kategori = ['setting'];
        return view('admin.pengaturan.pengaturan_aplikasi.index', compact('pengaturan', 'kategori'));
    }

    public function store(PengaturanAplikasiRequest $request)
    {
        try {
            foreach ($request->validated() as $key => $value) {
                PengaturanAplikasi::where(['key'=>$key])->update(['value'=> $value]);
            }
        } catch (\Throwable $th) {
            return back()->with('error', 'Data gagal diubah');
        }
        return redirect('pengaturan/aplikasi')->with('success', 'Data berhasil diubah');
    }
}
