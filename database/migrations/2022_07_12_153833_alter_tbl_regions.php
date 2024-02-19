<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_regions', function (Blueprint $table) {
            $table->string('new_region_name', 80)->nullable(true)->after('region_name');
            $table->integer('jenis')->default('0')->after('new_region_name');
            $table->text('keterangan')->nullable(true)->after('jenis');
            $table->timestamps();
            $table->integer('created_by')->nullable(true)->after('desa_id');
            $table->integer('updated_by')->nullable(true)->after('created_at');
        });

        // Tambahkan data awal
        DB::table('tbl_regions')->whereNull('created_at')->update(['created_at' => now(), 'updated_at' => now()]);

        if($user = User::first()){
            $user = $user->id;
            DB::table('tbl_regions')->whereNull('created_by')->update(['created_by' => $user, 'updated_by' => $user]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_regions', function ($table) {
            $table->dropColumn(['new_region_name', 'jenis', 'keterangan', 'created_by', 'updated_by', 'created_at', 'updated_at']);
        });
    }
};
