<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackOpendkRequest;
use App\Models\Opendk;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackOpendkController extends Controller
{
    public function __invoke(TrackOpendkRequest $request)
    {
        DB::beginTransaction();

        try {
            Opendk::query()->updateOrCreate(
                $request->requestWhere(),
                $request->requestData()
            );
            DB::commit();
            return response()->json(['status'=> true]);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json('Failed', 422);
        }
    }
}
