<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackRequest;
use App\Models\Akses;
use App\Models\Desa;
use App\Models\Notifikasi;
use App\Models\NotifikasiDesa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackController extends Controller
{
    public function __invoke(TrackRequest $request)
    {
        DB::beginTransaction();

        try {
            $desa = Desa::query()->updateOrCreate(
                $request->requestWhere(),
                $request->requestData()
            );

            $akses = Akses::query()->where('desa_id', $desa->id)->whereDate('tgl', date('Y-m-d'))->first();

            if ($akses) {
                $akses->tgl = $request->tgl;
                $akses->save();
            } else {
                Akses::create(
                    $request->merge(['desa_id' => $desa->id])->only(['desa_id', 'url_referrer', 'request_uri', 'client_ip', 'external_ip', 'opensid_version', 'tgl'])
                );
            }

            $notifikasi = Notifikasi::semuaNotifDesa($desa->id);
            NotifikasiDesa::nonAktifkan(collect($notifikasi), $desa->id);

            DB::commit();

            return response()->json($notifikasi);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json('Failed', 422);
        }
    }
}
