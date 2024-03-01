<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_regions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('region_code', 15)->unique('ix_region_code');
            $table->string('region_name', 80);
            $table->string('parent_code', 15)->nullable();
            $table->integer('desa_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_regions');
    }
};
