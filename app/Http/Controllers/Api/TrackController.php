<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackRequest;
use App\Models\Akses;
use App\Models\Desa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackController extends Controller
{
    public function __invoke(TrackRequest $request)
    {
        DB::beginTransaction();

        try {
            $desa = Desa::updateOrCreate(
                $request->only(['nama_desa', 'nama_kecamatan', 'nama_kabupaten', 'nama_provinsi']),
                $request->only(
                    [
                        'kode_pos',
                        'kode_desa',
                        'kode_kecamatan',
                        'kode_kabupaten',
                        'kode_provinsi',
                        'lat',
                        'lng',
                        'alamat_kantor',
                        // 'jenis',
                        'ip_lokal',
                        'ip_hosting',
                        'versi_lokal',
                        'versi_hosting',
                        'tgl_rekam_lokal',
                        'tgl_rekam_hosting',
                        'tgl_akses_lokal',
                        'tgl_akses_hosting',
                        'url_lokal',
                        'url_hosting',
                        // 'opensid_valid',
                        'email_desa',
                        'telepon',
                    ]
                )
            );

            Akses::create(
                $request->merge(['desa_id' => $desa->id])->only(['desa_id', 'url_referrer', 'request_uri', 'client_ip', 'external_ip', 'opensid_version', 'tgl'])
            );

            DB::commit();

        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json('Failed', 422);
        }

        return response()->json('ok');
    }
}
