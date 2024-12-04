<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opendk;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InstallOpenDKController extends Controller
{
    public function chart(Request $request)
    {
        $provinsi = $request->get('provinsi');
        $kabupaten = $request->get('kabupaten');
        $kecamatan = $request->get('kecamatan');
        $minCreatedAt = Carbon::now()->subYears(2)->format('Y-m-d');
        $opendk = Opendk::selectRaw("DATE_FORMAT(created_at, '%m-%Y') month_year, count(*) as total")
            ->groupBy('month_year')->orderBy('created_at')->whereDate('created_at', '>', $minCreatedAt);
        if ($provinsi) {
            $opendk->where('kode_provinsi', $provinsi);
        }
        if ($kabupaten) {
            $opendk->where('kode_kabupaten', $kabupaten);
        }
        if ($kecamatan) {
            $opendk->where('kode_kecamatan', $kecamatan);
        }

        $opendkData = $opendk->get();
        $labels = [];
        $datasetOpendk = [];
        foreach ($opendkData as $item) {
            $period = Carbon::createFromFormat('d-m-Y', '01-'.$item->month_year)->translatedFormat('M-y');
            $labels[] = $period;
            $datasetOpendk[] = $item->total;
        }
        $datasets = [
            ['label' => 'OpenDK', 'data' => $datasetOpendk],
        ];

        $result = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];

        return response()->json($result);
    }
}
