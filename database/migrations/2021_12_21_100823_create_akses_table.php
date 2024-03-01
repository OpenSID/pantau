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
        Schema::create('akses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->references('id')->on('desa')->onDelete('cascade');
            $table->string('url_referrer', 200);
            $table->string('request_uri', 200);
            $table->string('client_ip', 20);
            $table->string('external_ip', 20);
            $table->string('opensid_version', 20);
            $table->timestamp('tgl')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('akses');
    }
};
