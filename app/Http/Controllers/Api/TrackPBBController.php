<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackPBBRequest;
use App\Models\Pbb;
use Exception;
use Illuminate\Support\Facades\Log;

class TrackPBBController extends Controller
{
    public function __invoke(TrackPBBRequest $request)
    {
        try {
            Pbb::upsert($request->requestData(),['kode_desa']);

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json('Failed', 422);
        }
    }
}
