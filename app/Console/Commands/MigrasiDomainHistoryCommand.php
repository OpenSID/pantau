<?php

namespace App\Console\Commands;

use App\Models\Desa;
use App\Models\DomainHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrasiDomainHistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pantau:migrasi-domain-history
                            {--dry-run : Tampilkan rencana perubahan tanpa menyimpan ke database}
                            {--konsolidasi : Gabungkan data desa duplikat berdasarkan kode_desa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasikan riwayat domain desa, konsolidasi duplikat kode_desa, dan sinkronkan domain aktif';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('Mode dry-run: tidak ada perubahan yang disimpan.');
        }

        $this->sinkronDomainAktif($dryRun);

        if ($this->option('konsolidasi')) {
            $this->konsolidasiDuplikat($dryRun);
        }

        $this->tampilkanRingkasan();

        return self::SUCCESS;
    }

    /**
     * Isi riwayat domain dari desa yang belum punya riwayat aktif.
     */
    protected function sinkronDomainAktif(bool $dryRun): void
    {
        $desaList = Desa::query()
            ->whereNotNull('url_hosting')
            ->where('url_hosting', '!=', '')
            ->whereNotNull('kode_desa')
            ->whereDoesntHave('domainHistories', function ($query) {
                $query->where('is_active', true);
            })
            ->get(['id', 'kode_desa', 'url_hosting', 'domain', 'domain_updated_at', 'updated_at']);

        $this->info(sprintf('Desa tanpa riwayat domain aktif: %d', $desaList->count()));

        if ($dryRun || $desaList->isEmpty()) {
            return;
        }

        foreach ($desaList as $desa) {
            $normalized = fixDomainName($desa->url_hosting);

            DomainHistory::create([
                'desa_id' => $desa->id,
                'kode_desa' => $desa->kode_desa,
                'domain' => $desa->url_hosting,
                'domain_normalized' => $normalized,
                'is_active' => true,
                'source' => 'import',
                'obtained_at' => $desa->domain_updated_at ?? $desa->updated_at ?? now(),
                'note' => 'Migrasi dari data lama',
            ]);

            if (! $desa->domain) {
                $desa->domain = $desa->url_hosting;
                $desa->domain_updated_at = $desa->domain_updated_at ?? now();
                $desa->save();
            }
        }
    }

    /**
     * Gabungkan desa duplikat yang memiliki kode_desa sama.
     */
    protected function konsolidasiDuplikat(bool $dryRun): void
    {
        $duplikat = DB::table('desa')
            ->select('kode_desa', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('kode_desa')
            ->groupBy('kode_desa')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplikat->isEmpty()) {
            $this->info('Tidak ada desa duplikat berdasarkan kode_desa.');

            return;
        }

        $this->warn(sprintf('Ditemukan %d kode_desa dengan data ganda.', $duplikat->count()));

        foreach ($duplikat as $row) {
            $desaList = Desa::where('kode_desa', $row->kode_desa)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->get();

            $target = $desaList->first();
            $duplik = $desaList->slice(1);

            $this->line(sprintf('  %s -> target id=%d, duplikat: %s', $row->kode_desa, $target->id, $duplik->pluck('id')->implode(', ')));

            if ($dryRun) {
                continue;
            }

            DB::transaction(function () use ($target, $duplik) {
                foreach ($duplik as $desa) {
                    // Pindahkan riwayat domain duplikat ke desa target.
                    DomainHistory::where('desa_id', $desa->id)->update([
                        'desa_id' => $target->id,
                        'kode_desa' => $target->kode_desa,
                    ]);

                    // Hapus riwayat domain duplikat agar tidak ada domain aktif ganda.
                    DomainHistory::where('desa_id', $target->id)->where('is_active', true)
                        ->update(['is_active' => false]);

                    $aktif = DomainHistory::where('desa_id', $target->id)
                        ->orderByDesc('obtained_at')
                        ->orderByDesc('id')
                        ->first();

                    if ($aktif) {
                        DomainHistory::where('id', $aktif->id)->update(['is_active' => true]);
                        $target->domain = $aktif->domain;
                        $target->domain_updated_at = $aktif->obtained_at ?? now();
                        $target->save();
                    }

                    $desa->delete();
                }
            });
        }
    }

    /**
     * Tampilkan ringkasan kondisi data saat ini.
     */
    protected function tampilkanRingkasan(): void
    {
        $totalDesa = Desa::whereNotNull('kode_desa')->count();
        $totalHistory = DomainHistory::count();
        $totalAktif = DomainHistory::where('is_active', true)->count();
        $yatim = DomainHistory::whereNull('desa_id')->count();
        $ganda = DB::table('domain_histories')
            ->select('desa_id', DB::raw('COUNT(*) as jumlah'))
            ->where('is_active', true)
            ->whereNotNull('desa_id')
            ->groupBy('desa_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $this->newLine();
        $this->info('Ringkasan:');
        $this->line(sprintf('  Desa terdaftar          : %d', $totalDesa));
        $this->line(sprintf('  Riwayat domain          : %d', $totalHistory));
        $this->line(sprintf('  Domain aktif            : %d', $totalAktif));
        $this->line(sprintf('  Riwayat tanpa desa (yatim): %d', $yatim));
        $this->line(sprintf('  Desa dengan domain aktif ganda: %d', $ganda));
    }
}
