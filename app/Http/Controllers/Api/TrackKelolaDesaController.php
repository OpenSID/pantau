<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TrackKeloladesa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrackKelolaDesaRequest;
use Exception;

class TrackKelolaDesaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrackKelolaDesaRequest $request)
    {
        $request->validated();
        DB::beginTransaction();
        try {
            TrackKeloladesa::query()->updateOrCreate(
                $request->requestWhere(),
                $request->requestData()
            );

            DB::commit();

            return response()->json(['status' => "success"]);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json($e->getMessage(), 422);
        }
    }
}
