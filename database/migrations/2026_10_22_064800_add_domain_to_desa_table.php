<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('desa', function (Blueprint $table) {
            if (! Schema::hasColumn('desa', 'domain')) {
                $table->string('domain')->nullable()->after('url_hosting');
            }
            if (! Schema::hasColumn('desa', 'domain_updated_at')) {
                $table->timestamp('domain_updated_at')->nullable()->after('domain');
            }
        });
    }

    public function down()
    {
        Schema::table('desa', function (Blueprint $table) {
            if (Schema::hasColumn('desa', 'domain')) {
                $table->dropColumn('domain');
            }
            if (Schema::hasColumn('desa', 'domain_updated_at')) {
                $table->dropColumn('domain_updated_at');
            }
        });
    }
};
