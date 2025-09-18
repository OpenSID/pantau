<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrasi data dari user_grup ke roles
        $userGrups = DB::table('user_grup')->get();

        foreach ($userGrups as $grup) {
            Role::create([
                'name' => $grup->nama,
                'guard_name' => 'web'
            ]);
        }

        // Migrasi data users dari id_grup ke roles
        $users = DB::table('users')->get();

        foreach ($users as $userData) {
            if ($userData->id_grup) {
                $grup = DB::table('user_grup')->where('id', $userData->id_grup)->first();
                if ($grup) {
                    $user = User::find($userData->id);
                    if ($user) {
                        $user->assignRole($grup->nama);
                    }
                }
            }
        }

        // Hapus kolom id_grup dari users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id_grup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tambahkan kembali kolom id_grup
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_grup')->nullable();
        });

        // Restore data dari roles ke id_grup
        $users = User::with('roles')->get();

        foreach ($users as $user) {
            if ($user->roles->isNotEmpty()) {
                $roleName = $user->roles->first()->name;
                $grup = DB::table('user_grup')->where('nama', $roleName)->first();
                if ($grup) {
                    DB::table('users')->where('id', $user->id)->update(['id_grup' => $grup->id]);
                }
            }
        }

        // Hapus roles yang dibuat
        Role::whereIn('name', DB::table('user_grup')->pluck('nama'))->delete();
    }
};
