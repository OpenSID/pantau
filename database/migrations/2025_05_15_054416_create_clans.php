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
        $this->down();
        Schema::create('clans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ethnic_group_id');
            $table->string('name', 100);
            $table->timestamps();
            $table->foreign('ethnic_group_id')
                ->references('id')->on('ethnic_groups')
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
        Schema::dropIfExists('clans');
    }
};
