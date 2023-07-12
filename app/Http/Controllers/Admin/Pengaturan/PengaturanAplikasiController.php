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
            if (! $request->get('abaikan_domain_opendk')) {
                $request->merge(['abaikan_domain_opendk' => null]);
            }
            if (! $request->get('abaikan_domain_opensid')) {
                $request->merge(['abaikan_domain_opensid' => null]);
            }
            foreach ($request->all() as $key => $value) {
                if (is_array($value)) {
                    switch ($key) {
                        case 'abaikan_domain_opendk':
                        case 'abaikan_domain_opensid':
                            $value = $value ? implode('|', $value) : null;
                            break;
                        default:
                            $value = collect($value)->map(function ($item) {
                                return json_decode($item);
                            })->toJson();
                    }
                }
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
            $wilayahKhusus = [];
            $tmp = PengaturanAplikasi::where(['key' => 'wilayah_khusus', 'kategori' => 'setting'])->select(['value'])->first();
            if (! empty($tmp->value)) {
                foreach (json_decode($tmp->value) as $key => $val) {
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
