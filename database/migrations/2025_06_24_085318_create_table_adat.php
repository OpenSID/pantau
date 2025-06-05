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
        Schema::create('adats', function (Blueprint $table) {
            $table->id();
            $table->integer('tbl_region_id');
            $table->string('name', 100);
            $table->timestamps();
            $table->unique(['tbl_region_id', 'name']);
            $table->foreign('tbl_region_id')
                ->references('id')->on('tbl_regions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adats');
    }
};
