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
        $kategori = ['setting', 'wilayah_khusus'];

        return view('admin.pengaturan.pengaturan_aplikasi.index', compact('pengaturan', 'kategori'));
    }

    public function store(PengaturanAplikasiRequest $request)
    {
        try {
            if (! $request->get('wilayah_khusus')) {
                $request->merge(['wilayah_khusus' => '[]']);
            }
            foreach ($request->all() as $key => $value) {
                if ( is_array($value)) {
                    $value = collect($value)->map(function($item){
                        return json_decode($item);
                    })->toJson();
                }
                PengaturanAplikasi::where(['key' => $key])->update(['value' => $value]);
            }
            $wilayahKhusus = [];
            $tmp = PengaturanAplikasi::where(['key' => 'wilayah_khusus', 'kategori' => 'setting'])->select(['value'])->first();
            if (!empty ($tmp->value)){
                foreach(json_decode($tmp->value) as $key => $val) {
                    $wilayahKhusus[$val->key] = $val->value;
                }
            }

            Cache::forever('pantau_wilayah_khusus', $wilayahKhusus);
        } catch (\Throwable $th) {
            return back()->with('error', 'Data gagal diubah');
        }

        return redirect('pengaturan/aplikasi')->with('success', 'Data berhasil diubah');
    }
}
