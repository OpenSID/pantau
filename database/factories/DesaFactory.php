<?php

namespace Database\Factories;

use App\Models\Desa;
use Illuminate\Database\Eloquent\Factories\Factory;

class DesaFactory extends Factory
{
    protected $model = Desa::class;

    public function definition()
    {
        return [
            'nama_desa' => $this->faker->unique()->word(),
            'kode_desa' => $this->faker->numerify('##########'),
            'kode_pos' => $this->faker->postcode(),
            'nama_kecamatan' => $this->faker->word(),
            'kode_kecamatan' => $this->faker->numerify('########'),
            'nama_kabupaten' => $this->faker->word(),
            'kode_kabupaten' => $this->faker->numerify('########'),
            'nama_provinsi' => $this->faker->word(),
            'kode_provinsi' => $this->faker->numerify('########'),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'alamat_kantor' => $this->faker->address(),
            'jenis' => $this->faker->randomElement(['desa', 'kelurahan']),
            'ip_lokal' => $this->faker->ipv4(),
            'ip_hosting' => $this->faker->ipv4(),
            'versi_lokal' => $this->faker->randomElement(['2507.0.0', '2403.0.0', '2402.0.0']),
            'versi_hosting' => $this->faker->randomElement(['2507.0.0', '2403.0.0', '2402.0.0']),
            'tgl_rekam_lokal' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'tgl_rekam_hosting' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'tgl_akses_lokal' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'tgl_akses_hosting' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'url_lokal' => $this->faker->url(),
            'url_hosting' => $this->faker->url(),
            'opensid_valid' => $this->faker->boolean(),
            'email_desa' => $this->faker->unique()->safeEmail(),
            'telepon' => $this->faker->phoneNumber(),
            'jml_surat_tte' => $this->faker->numberBetween(0, 100),
            'modul_tte' => $this->faker->boolean(),
            'jml_keluarga' => $this->faker->numberBetween(0, 1000),
            'tema' => $this->faker->randomElement(['esensi', 'natra', 'palanta', 'batuah', 'denatra']),
        ];
    }
}
