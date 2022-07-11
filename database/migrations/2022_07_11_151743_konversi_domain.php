<?php

use App\Models\Desa;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KonversiDomain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $semuaDesa = Desa::select('id', 'url_lokal', 'url_hosting')->whereNotNUll('url_lokal')->whereNotNUll('url_hosting')->get();

        foreach ($semuaDesa as $desa) {
            Desa::where('id', $desa->id)
                ->update([
                    'url_lokal'   => fixDomainName($desa->url_lokal),
                    'url_hosting' => fixDomainName($desa->url_hosting),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
