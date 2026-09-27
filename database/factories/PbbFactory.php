<?php

namespace Database\Factories;

use App\Models\Pbb;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbbFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pbb::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_desa' => $this->faker->numerify('##.##.##.####'),
            'nama_desa' => $this->faker->city,
            'kode_kecamatan' => $this->faker->numerify('##.##.##'),
            'nama_kecamatan' => $this->faker->city,
            'kode_kabupaten' => $this->faker->numerify('##.##'),
            'nama_kabupaten' => $this->faker->city,
            'kode_provinsi' => $this->faker->numerify('##'),
            'nama_provinsi' => $this->faker->state,
            'versi' => $this->faker->randomElement(['1.0.0', '1.1.0', '2.0.0']),
            'updated_at' => now(),
            'created_at' => now(),
        ];
    }
}
