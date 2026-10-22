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
            $desa = Desa::where('kode_desa', $kodeDesa)->first();

            // Check if current domain is same as new domain
            if ($desa) {
                $currentNormalized = $desa->domain ? fixDomainName($desa->domain) : null;
                if ($currentNormalized === $normalizedDomain) {
                    // Update obtained_at for existing active history if needed
                    $activeHistory = DomainHistory::where('desa_id', $desa->id)
                        ->where('is_active', true)
                        ->first();
                    if ($activeHistory) {
                        if (! $activeHistory->obtained_at) {
                            $activeHistory->obtained_at = now();
                            $activeHistory->save();
                        }
                    }

                    return $activeHistory;
                }

                // Deactivate all active histories for this desa
                DomainHistory::where('desa_id', $desa->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

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
     * Manually set a domain history as active.
     */
    public function setActive(DomainHistory $history): bool
    {
        return DB::transaction(function () use ($history) {
            if (! $history->desa_id && $history->kode_desa) {
                $desa = Desa::where('kode_desa', $history->kode_desa)->first();
                if ($desa) {
                    $history->desa_id = $desa->id;
                    $history->save();
                }
            }

            if ($history->desa_id) {
                // Deactivate all active histories for this desa
                DomainHistory::where('desa_id', $history->desa_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                // Activate this one
                DomainHistory::where('id', $history->id)->update([
                    'is_active' => true,
                    'obtained_at' => $history->obtained_at ?? now(),
                    'updated_at' => now(),
                ]);
                $history->refresh();

                // Update desa domain
                $desa = Desa::find($history->desa_id);
                if ($desa) {
                    $desa->domain = $history->domain;
                    $desa->domain_updated_at = now();
                    $desa->save();
                }

                return true;
            }

            return false;
        });
    }

    /**
     * Add manual domain entry.
     */
    public function addManualDomain(Desa $desa, string $domain, ?string $note = null): DomainHistory
    {
        return $this->recordDomainChange($desa->kode_desa, $domain, 'manual', $note);
    }
}
