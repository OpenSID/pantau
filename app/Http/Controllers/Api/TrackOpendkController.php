<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Opendk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrackOpendkRequest;

class TrackOpendkController extends Controller
{
    public function __invoke(TrackOpendkRequest $request){
        DB::beginTransaction();

        try {
            Opendk::query()->updateOrCreate(
                $request->requestWhere(),
                $request->requestData()
            );
            DB::commit();
            return response()->json(['status'=> '-']);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json('Failed', 422);
        }
    }
}
