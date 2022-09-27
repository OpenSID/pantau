<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackOpendkRequest;
use App\Models\Opendk;
use Exception;
use Illuminate\Support\Facades\Log;

class TrackOpendkController extends Controller
{
    public function __invoke(TrackOpendkRequest $request)
    {
        try {
            Opendk::query()->updateOrCreate(
                $request->requestWhere(),
                $request->requestData()
            );

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json('Failed', 422);
        }
    }
}
