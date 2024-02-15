<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackKelolaDesaRequest;
use App\Models\TrackKeloladesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackKelolaDesaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrackKelolaDesaRequest $request)
    {
        $request->validated();
        DB::beginTransaction();
        try {
            TrackKeloladesa::updateOrCreate(
                $request->requestWhere(),
                $request->requestData()
            );

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json('Failed', 422);
        }
    }
}
