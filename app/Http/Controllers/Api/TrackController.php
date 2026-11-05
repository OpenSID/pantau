<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackRequest;
use App\Models\Akses;
use App\Models\Desa;
use App\Models\Notifikasi;
use App\Models\NotifikasiDesa;
use App\Notifications\InfoNotification;
use App\Services\DomainHistoryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TrackController extends Controller
{
    public function __invoke(TrackRequest $request, DomainHistoryService $domainHistoryService)
    {
        DB::beginTransaction();

        try {
            $kodeDesaTracking = $request->input('kode_desa');
            if ($kodeDesaTracking) {
                $kodeDesaTracking = str_replace('.', '', (string) $kodeDesaTracking);
                // Format ulang seperti biasa jika perlu, tapi lebih aman ambil dari requestWhere
            }
            $requestWhere = $request->requestWhere();
            $desaLama = isset($requestWhere['kode_desa']) ? Desa::where('kode_desa', $requestWhere['kode_desa'])->first() : null;
            $urlHostLama = $desaLama?->url_hosting;

            $desa = Desa::query()->updateOrCreate(
                $requestWhere,
                $request->requestData()
            );

            // Catat riwayat domain desa hanya jika url_hosting berubah atau desa baru.
            // Pencatatan riwayat bersifat best-effort agar kegagalan migrasi/skema
            // tidak menggagalkan seluruh proses tracking OpenSID.
            $urlHostBaru = $desa->url_hosting;
            $domainBerubah = $desa->wasRecentlyCreated || ($urlHostLama !== $urlHostBaru);

            if ($domainBerubah && $urlHostBaru) {
                $domain = fixDomainName($urlHostBaru);

                if ($domain && ! is_local($urlHostBaru)) {
                    try {
                        if (DB::transactionLevel() > 0 && DB::getDriverName() !== 'sqlite') {
                            DB::statement('SAVEPOINT sp_domain_hist');
                            try {
                                $domainHistoryService->recordDomainChange($desa->kode_desa, $urlHostBaru, 'crawler');
                                DB::statement('RELEASE SAVEPOINT sp_domain_hist');
                            } catch (\Throwable $e) {
                                DB::statement('ROLLBACK TO SAVEPOINT sp_domain_hist');
                                DB::statement('RELEASE SAVEPOINT sp_domain_hist');
                                Log::warning('Gagal mencatat riwayat domain saat tracking desa: '.$desa->kode_desa, [
                                    'domain' => $domain,
                                    'message' => $e->getMessage(),
                                ]);
                            }
                        } else {
                            $domainHistoryService->recordDomainChange($desa->kode_desa, $urlHostBaru, 'crawler');
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Gagal mencatat riwayat domain saat tracking desa: '.$desa->kode_desa, [
                            'domain' => $domain,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            } elseif ($domainBerubah && ! $urlHostBaru && $request->filled('url')) {
                $domain = fixDomainName($request->url);

                if ($domain && ! is_local($request->url)) {
                    try {
                        if (DB::transactionLevel() > 0 && DB::getDriverName() !== 'sqlite') {
                            DB::statement('SAVEPOINT sp_domain_hist');
                            try {
                                $domainHistoryService->recordDomainChange($desa->kode_desa, $request->url, 'crawler');
                                DB::statement('RELEASE SAVEPOINT sp_domain_hist');
                            } catch (\Throwable $e) {
                                DB::statement('ROLLBACK TO SAVEPOINT sp_domain_hist');
                                DB::statement('RELEASE SAVEPOINT sp_domain_hist');
                                Log::warning('Gagal mencatat riwayat domain saat tracking desa: '.$desa->kode_desa, [
                                    'domain' => $domain,
                                    'message' => $e->getMessage(),
                                ]);
                            }
                        } else {
                            $domainHistoryService->recordDomainChange($desa->kode_desa, $request->url, 'crawler');
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Gagal mencatat riwayat domain saat tracking desa: '.$desa->kode_desa, [
                            'domain' => $domain,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            }

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
