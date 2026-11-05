<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('domain_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desa_id')->nullable();
            $table->string('kode_desa')->index();
            $table->string('domain');
            $table->string('domain_normalized')->index();
            $table->boolean('is_active')->default(false);
            $table->string('source')->nullable();
            $table->timestamp('obtained_at')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('set null');
            $table->index('desa_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('domain_histories');
    }
};
