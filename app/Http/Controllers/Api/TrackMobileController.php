<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackMobileRequest;
use App\Models\TrackMobile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackMobileController extends Controller
{
    public function __invoke(TrackMobileRequest $request)
    {
        DB::beginTransaction();
        try {
            TrackMobile::query()->updateOrCreate(
                $request->requestWhere(),
                $request->requestData()
            );

            DB::commit();

            return response()->json([]);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json('Failed', 422);
        }
    }
}
