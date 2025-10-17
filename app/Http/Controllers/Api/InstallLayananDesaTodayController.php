<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrackMobileView;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InstallLayananDesaTodayController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {                
        $installHariIni = TrackMobileView::filterWilayah($request)->whereDate('created_at', '>=', Carbon::now()->format('Y-m-d'))->get()->map(function($item) {
            $item->created_at_format_human = formatDateTimeForHuman($item->created_at);
            return $item;                
        });

        return [     
            'installHariIni' => $installHariIni,
        ];
    }
}
