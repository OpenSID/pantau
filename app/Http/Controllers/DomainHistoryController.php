<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetActiveDomainHistoryRequest;
use App\Http\Requests\StoreDomainHistoryRequest;
use App\Models\Desa;
use App\Models\DomainHistory;
use App\Services\DomainHistoryService;

class DomainHistoryController extends Controller
{
    protected $domainHistoryService;

    public function __construct(DomainHistoryService $domainHistoryService)
    {
        $this->domainHistoryService = $domainHistoryService;
    }

    public function index(Desa $desa)
    {
        $histories = $this->domainHistoryService->getDomainHistory($desa);

        return view('desa.domain-history.index', compact('desa', 'histories'));
    }

    public function store(StoreDomainHistoryRequest $request, Desa $desa)
    {
        $domain = $request->getNormalizedDomain();

        $this->domainHistoryService->addManualDomain($desa, $domain, $request->note);

        return redirect()->back()->with('success', 'Domain berhasil dicatat.');
    }

    public function setActive(SetActiveDomainHistoryRequest $request, Desa $desa, DomainHistory $history)
    {
        $berhasil = $this->domainHistoryService->setActive($desa, $history);

        if (! $berhasil) {
            return redirect()->back()->with('error', 'Domain tidak dapat diaktifkan untuk desa ini.');
        }

        return redirect()->back()->with('success', 'Domain aktif berhasil diubah.');
    }

    public function download(Desa $desa)
    {
        $histories = $this->domainHistoryService->getDomainHistory($desa);
        $filename = 'domain-history-'.$desa->kode_desa.'-'.date('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($histories) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Desa', 'Domain', 'Domain Normalized', 'Status', 'Source', 'Diperoleh Pada', 'Catatan']);

            foreach ($histories as $history) {
                fputcsv($file, [
                    $history->kode_desa,
                    $history->domain,
                    $history->domain_normalized,
                    $history->is_active ? 'Aktif' : 'Tidak Aktif',
                    $history->source,
                    $history->obtained_at,
                    $history->note,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Daftar riwayat domain yang belum terpetakan ke desa (desa_id null).
     */
    public function orphan()
    {
        $orphans = DomainHistory::whereNull('desa_id')
            ->orderByDesc('obtained_at')
            ->orderByDesc('id')
            ->paginate(50);

        return view('desa.domain-history.orphan', compact('orphans'));
    }
}
