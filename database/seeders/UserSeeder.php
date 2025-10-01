<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * id_grup dihapus pada database/migrations/2025_09_11_060000_migrate_user_grup_to_roles.php
     * @return void
     */
    public function run()
    {
        $users = [
            [
                "name" => "Eddie Ridwan",
                "username" => "eddieridwan",
                "email" => "eddie.ridwan@gmail.com",
                "avatar" => "default.jpg",
                "password" => '$2y$10$xyNmjtuWL3.apmIgQGZ2y.c8X908ym8PlbkZQPCB4iJoVHq90Fv8q',
                "token" => null,
                "created_at" => now(),
            ],
            [
                "name" => "Herry Wanda",
                "username" => "herrywanda",
                "email" => "herrywanda@gmail.com	",
                "avatar" => "default.jpg",
                "password" => '$2y$10$b6IFYRt9th6nr1S2dfbb4epFIFpy.QcXbN.iB7SzMJuFO4BjePdq6',
                "token" => null,
                "created_at" => now(),
            ],
            [

                "name" => "Rudi Purwanto",
                "username" => "roaddee",
                "email" => "rudi.purwanto@opendesa.id",
                "avatar" => "default.jpg",
                "password" => '$2y$10$V7iREjPiUnjvVkIOp9iPIeV72wb/k0z4NBXwT9raUT4XpvVYnWvOa',
                "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6bnVsbCwidGltZXN0YW1wIjoxNjE0MjM5ODQzfQ.HlzpyJgG431dw17idGuU70b1FJXW7ZrmRZRzsC9jyIU',
                "created_at" => now(),
            ],
            [

                "name" => "Muhammad Ihsan",
                "username" => "muhammadihsan",
                "email" => "ihsan2857@gmail.com	",
                "avatar" => "default.jpg",
                "password" => "$2y$10$1Nikfgo/0yYjQGVDb1vGhegoXyFTiqYfCaTx4Xc8RlB4zcK6kSPie",
                "token" => null,
                "created_at" => now(),
            ],
            [

                "name" => "Muhammad AI",
                "username" => "aiskematik",
                "email" => "mohhammadaidin@gmail.com",
                "avatar" => "default.jpg",
                "password" => "$2y$10$/TyNRWV5NJN4zekzIaEMbO/mzwc9HunTmNoUAnaKl9x1AMbd3/4vW",
                "token" => null,
                "created_at" => now(),
            ],
            [
                "name" => "Agung Sugiarto",
                "username" => "agungsugiarto",
                "email" => "me.agungsugiarto@gmail.com",
                "avatar" => "default.jpg",
                "password" => '$2y$10$u9P4w37ps8jS1mwFdgsWuOYdRha2UYTRyhLaKCUpqj3I4dl9PY4IW',
                "token" => null,
                "created_at" => now(),
            ],
            [

                "name" => "Andi Fahruddin Akas",
                "username" => "andifahruddinakas",
                "email" => "andifahruddinakas@gmail.com",
                "avatar" => "default.jpg",
                "password" => '$2y$10$frHX2pWN3XQiZeljnpDtnOkMqWpTnajYVjFeKNR9K04oxlkyqpf9u',
                "token" => null,
                "created_at" => now(),
            ],
        ];

        DB::table('users')->insert($users);
        $firstRole = \Spatie\Permission\Models\Role::first();
        if ($firstRole) {
            $userIds = DB::table('users')->pluck('id');
            foreach ($userIds as $userId) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->assignRole($firstRole);
                }
            }
        }
    }
}
