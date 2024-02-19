<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                "id_grup" => 1,
                "name" => "Eddie Ridwan",
                "username" => "eddieridwan",
                "email" => "eddie.ridwan@gmail.com",
                "avatar" => "default.jpg",
                "password" => '$2y$10$xyNmjtuWL3.apmIgQGZ2y.c8X908ym8PlbkZQPCB4iJoVHq90Fv8q',
                "token" => null,
                "created_at" => now(),
            ],
            [
                "id_grup" => 1,
                "name" => "Herry Wanda",
                "username" => "herrywanda",
                "email" => "herrywanda@gmail.com	",
                "avatar" => "default.jpg",
                "password" => '$2y$10$b6IFYRt9th6nr1S2dfbb4epFIFpy.QcXbN.iB7SzMJuFO4BjePdq6',
                "token" => null,
                "created_at" => now(),
            ],
            [
                "id_grup" => 1,
                "name" => "Rudi Purwanto",
                "username" => "roaddee",
                "email" => "rudi.purwanto@opendesa.id",
                "avatar" => "default.jpg",
                "password" => '$2y$10$V7iREjPiUnjvVkIOp9iPIeV72wb/k0z4NBXwT9raUT4XpvVYnWvOa',
                "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6bnVsbCwidGltZXN0YW1wIjoxNjE0MjM5ODQzfQ.HlzpyJgG431dw17idGuU70b1FJXW7ZrmRZRzsC9jyIU',
                "created_at" => now(),
            ],
            [
                "id_grup" => 1,
                "name" => "Muhammad Ihsan",
                "username" => "muhammadihsan",
                "email" => "ihsan2857@gmail.com	",
                "avatar" => "default.jpg",
                "password" => "$2y$10$1Nikfgo/0yYjQGVDb1vGhegoXyFTiqYfCaTx4Xc8RlB4zcK6kSPie",
                "token" => null,
                "created_at" => now(),
            ],
            [
                "id_grup" => 1,
                "name" => "Muhammad AI",
                "username" => "aiskematik",
                "email" => "mohhammadaidin@gmail.com",
                "avatar" => "default.jpg",
                "password" => "$2y$10$/TyNRWV5NJN4zekzIaEMbO/mzwc9HunTmNoUAnaKl9x1AMbd3/4vW",
                "token" => null,
                "created_at" => now(),
            ],
            [
                "id_grup" => 1,
                "name" => "Agung Sugiarto",
                "username" => "agungsugiarto",
                "email" => "me.agungsugiarto@gmail.com",
                "avatar" => "default.jpg",
                "password" => '$2y$10$u9P4w37ps8jS1mwFdgsWuOYdRha2UYTRyhLaKCUpqj3I4dl9PY4IW',
                "token" => null,
                "created_at" => now(),
            ],
            [
                "id_grup" => 1,
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
    }
}
