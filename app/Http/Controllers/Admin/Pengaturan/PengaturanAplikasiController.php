<?php

namespace App\Http\Controllers\Admin\Pengaturan;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengaturanAplikasiRequest;
use App\Models\PengaturanAplikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PengaturanAplikasiController extends Controller
{
    public function index(Request $request)
    {
        $pengaturan = PengaturanAplikasi::where(['kategori' => 'setting'])->get();
        $kategori = ['setting'];
        \Log::error('opensid '. abaikan_domain('opensid'));
        \Log::error('opendk '. abaikan_domain('opendk'));
        return view('admin.pengaturan.pengaturan_aplikasi.index', compact('pengaturan', 'kategori'));
    }

    public function store(PengaturanAplikasiRequest $request)
    {
        try {
            foreach ($request->all() as $key => $value) {
                PengaturanAplikasi::where(['key' => $key])->update(['value' => $value]);

                switch($key) {
                    case 'abaikan_domain_opendk':
                        Cache::forever('abaikan_domain_opendk', $value);
                        break;
                    case 'abaikan_domain_opensid':
                        Cache::forever('abaikan_domain_opensid', $value);
                        break;
                }
            }

        } catch (\Throwable $th) {
            return back()->with('error', 'Data gagal diubah');
        }

        return redirect('pengaturan/aplikasi')->with('success', 'Data berhasil diubah');
    }
}
