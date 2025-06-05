<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ethnic_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('adat_id')->nullable()->after('id');
            $table->foreign('adat_id')
                ->references('id')->on('adats')
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
        // Drop foreign key dan kolom adat_id dari tabel ethnic
        Schema::table('ethnic', function (Blueprint $table) {
            $table->dropForeign(['adat_id']);
            $table->dropColumn('adat_id');
        });
    }
};
