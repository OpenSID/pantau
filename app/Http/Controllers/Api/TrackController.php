<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackRequest;
use App\Models\Akses;
use App\Models\Desa;
use App\Models\Notifikasi;
use App\Models\NotifikasiDesa;
use App\Notifications\InfoNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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

            if ($desa->isPemdaHosting() && $desa->isNew()) {
                $userIdTelegram = Cache::get('id_telegram');
                $tokenBotTelegram = Cache::get('token_bot_telegram');
                if ($tokenBotTelegram && $userIdTelegram) {
                    $message = <<<STR
Pemasangan OpenSID oleh diskominfo                        
Kode desa : {$desa->kode_desa}
Nama desa/kec/kabupaten/provinsi : {$desa->nama_desa}/{$desa->nama_kecamatan}/{$desa->nama_kabupaten}/{$desa->nama_provinsi}
Tanggal/waktu terpantau : {$desa->created_at}
Domain dan IP Address : {$desa->url_hosting} / {$desa->ip_hosting}
STR;

                    foreach ($userIdTelegram as $key => $id) {
                        Notification::route('telegram', 'Pantau Notifikasi')->notify(new InfoNotification($id, $message));
                    }
                }
            }

            return response()->json($notifikasi);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json('Failed', 422);
        }
    }
}
