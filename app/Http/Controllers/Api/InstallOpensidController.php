<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InstallOpensidController extends Controller
{
    public function chart(Request $request)
    {
        $minCreatedAt = Carbon::now()->subYears(2)->format('Y-m-d');
        $opensid = Desa::filterWilayah($request)->selectRaw("DATE_FORMAT(created_at, '%m-%Y') month_year, count(*) as total")
            ->groupBy('month_year')->orderBy('created_at')->whereDate('created_at', '>', $minCreatedAt);

        $opensidData = $opensid->get();
        $labels = [];
        $datasetOpensid = [];
        foreach ($opensidData as $item) {
            $period = Carbon::createFromFormat('d-m-Y', '01-'.$item->month_year)->translatedFormat('M-y');
            $labels[] = $period;
            $datasetOpensid[] = $item->total;
        }
        $datasets = [
            ['label' => 'OpenSID', 'data' => $datasetOpensid],
        ];

        $result = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];

        return response()->json($result);
    }
}
