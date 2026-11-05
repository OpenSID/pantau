<?php

namespace App\Services;

use App\Models\Desa;
use App\Models\DomainHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DomainHistoryService
{
    /**
     * Record domain change for a village identified by kode_desa.
     */
    public function recordDomainChange(string $kodeDesa, string $domain, string $source = 'crawler', ?string $note = null, ?int $createdBy = null): ?DomainHistory
    {
        $normalizedDomain = fixDomainName($domain);

        if (empty($normalizedDomain)) {
            return null;
        }

        return DB::transaction(function () use ($kodeDesa, $domain, $normalizedDomain, $source, $note, $createdBy) {
            // lockForUpdate mencegah dua track bersamaan untuk kode_desa yang sama
            // menyisakan dua baris dengan is_active = true.
            $desa = Desa::where('kode_desa', $kodeDesa)->lockForUpdate()->first();

            // Check if current domain is same as new domain
            if ($desa) {
                // Deactivate all active histories for this desa
                DomainHistory::where('desa_id', $desa->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false, 'updated_at' => now()]);

                // Create new active history
                $history = DomainHistory::create([
                    'desa_id' => $desa->id,
                    'kode_desa' => $kodeDesa,
                    'domain' => $domain,
                    'domain_normalized' => $normalizedDomain,
                    'is_active' => true,
                    'source' => $source,
                    'obtained_at' => now(),
                    'note' => $note,
                    'created_by' => $createdBy ?? (Auth::check() ? Auth::id() : null),
                ]);

                // Update desa domain
                $desa->domain = $domain;
                $desa->domain_updated_at = now();
                $desa->save();

                return $history;
            } else {
                // No desa found by kode_desa, create history with desa_id null
                $history = DomainHistory::create([
                    'desa_id' => null,
                    'kode_desa' => $kodeDesa,
                    'domain' => $domain,
                    'domain_normalized' => $normalizedDomain,
                    'is_active' => false, // Not active since no desa mapped
                    'source' => $source,
                    'obtained_at' => now(),
                    'note' => $note ?? 'No desa found for this kode_desa',
                    'created_by' => $createdBy ?? (Auth::check() ? Auth::id() : null),
                ]);

                return $history;
            }
        });
    }

    /**
     * Get domain history for a desa.
     */
    public function getDomainHistory(Desa $desa)
    {
        return DomainHistory::where('desa_id', $desa->id)
            ->orWhere('kode_desa', $desa->kode_desa)
            ->orderBy('obtained_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Manually set a domain history as active for a given desa.
     *
     * Riwayat yang tidak dimiliki desa tersebut (desa_id menunjuk desa lain)
     * akan ditolak agar perubahan domain tidak bisa diarahkan ke desa lain.
     */
    public function setActive(Desa $desa, DomainHistory $history): bool
    {
        return DB::transaction(function () use ($desa, $history) {
            $history = DomainHistory::where('id', $history->id)
                ->where(function ($query) use ($desa) {
                    $query->where('desa_id', $desa->id)
                        ->orWhere(function ($query) use ($desa) {
                            $query->whereNull('desa_id')
                                ->where('kode_desa', $desa->kode_desa);
                        });
                })
                ->first();

            if (! $history) {
                return false;
            }

            // Riwayat yatim (desa_id null) dengan kode_desa yang sama diadopsi
            // oleh desa ini.
            if (! $history->desa_id) {
                $history->desa_id = $desa->id;
                $history->save();
            }

            $desaAktif = Desa::whereKey($desa->id)->lockForUpdate()->first();

            if (! $desaAktif) {
                return false;
            }

            // Deactivate all active histories for this desa
            DomainHistory::where('desa_id', $desa->id)
                ->where('is_active', true)
                ->update(['is_active' => false, 'updated_at' => now()]);

            // Activate this one
            DomainHistory::where('id', $history->id)->update([
                'is_active' => true,
                'obtained_at' => $history->obtained_at ?? now(),
                'updated_at' => now(),
            ]);
            $history->refresh();

            // Update desa domain
            $desaAktif->domain = $history->domain;
            $desaAktif->domain_updated_at = now();
            $desaAktif->save();

            return true;
        });
    }

    /**
     * Add manual domain entry.
     */
    public function addManualDomain(Desa $desa, string $domain, ?string $note = null): ?DomainHistory
    {
        return $this->recordDomainChange($desa->kode_desa, $domain, 'manual', $note);
    }
}
