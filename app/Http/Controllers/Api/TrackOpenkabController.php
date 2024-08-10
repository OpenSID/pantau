<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Openkab;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\TrackOpenkabRequest;

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
