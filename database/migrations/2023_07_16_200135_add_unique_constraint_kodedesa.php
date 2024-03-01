<?php

use App\Models\Desa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $kembar = DB::table('desa')
                ->groupBy('kode_desa')
                ->having(DB::raw('count(kode_desa)'), '>', 1)
                ->pluck('kode_desa');
        if ($kembar) {
            $hapus = Desa::select(['id', 'kode_desa', 'updated_at'])->whereIn('kode_desa', $kembar->toArray())->orderBy('updated_at', 'desc')->get()->groupBy('kode_desa')->map(function($item) {
                $item->shift();
                return $item->pluck('id');
            });
            Desa::whereIn('id', $hapus->flatten()->all())->delete();
        }

        Schema::table('desa', function(Blueprint $table){
            $table->unique('kode_desa', 'uq_desa_kode_desa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('desa', function(Blueprint $table){
            $table->dropUnique('uq_desa_kode_desa');
        });
    }
};
