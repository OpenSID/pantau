<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackOpenkabRequest;
use App\Models\Openkab;
use Exception;
use Illuminate\Support\Facades\Log;

class TrackOpenkabController extends Controller
{
    public function __invoke(TrackOpenkabRequest $request)
    {
        try {
            Openkab::query()->updateOrCreate(
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
