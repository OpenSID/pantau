<?php

namespace Database\Factories;

use App\Models\Openkab;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpenkabFactory extends Factory
{
    protected $model = Openkab::class;

    public function definition(): array
    {
        return [
            'kode_kab' => $this->faker->unique()->numerify('##01'),
            'nama_kab' => $this->faker->city,
            'kode_prov' => $this->faker->numerify('##'),
            'nama_prov' => $this->faker->state,
            'nama_aplikasi' => 'OpenKab',
            'sebutan_kab' => 'Kabupaten',
            'url' => $this->faker->url,
            'versi' => $this->faker->randomElement(['23.01', '23.02', '23.03', '24.01']),
            'jumlah_desa' => $this->faker->numberBetween(10, 500),
            'jumlah_penduduk' => $this->faker->numberBetween(50000, 2000000),
            'jumlah_keluarga' => $this->faker->numberBetween(10000, 500000),
            'jumlah_rtm' => $this->faker->numberBetween(8000, 400000),
            'jumlah_bantuan' => $this->faker->numberBetween(100, 10000),
            'tgl_rekam' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
