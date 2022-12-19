<?php

namespace App\Http\Controllers\Admin\Pengaturan;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengaturanAplikasiRequest;
use App\Models\PengaturanAplikasi;
use Illuminate\Http\Request;

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
                PengaturanAplikasi::where(['key' => $key])->update(['value' => $value]);
            }
        } catch (\Throwable $th) {
            return back()->with('error', 'Data gagal diubah');
        }

        return redirect('pengaturan/aplikasi')->with('success', 'Data berhasil diubah');
    }
}
