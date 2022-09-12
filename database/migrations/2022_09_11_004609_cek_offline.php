<?php

use App\Models\Desa;
use App\Models\LogUrlHosting;
use App\Http\Requests\TrackRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Schema\Blueprint;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Database\Eloquent\MassAssignmentException;

class CekOffline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // tambahkan log url hosting yang diupdate 1 bulan sekali.
        Schema::create('log_url_hosting', function (Blueprint $table) {
            $table->string('url')->unique();
            $table->smallInteger('status');
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
        Schema::dropIfExists('log_url_hosting');
    }
}
