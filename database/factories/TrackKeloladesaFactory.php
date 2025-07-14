<?php

namespace Database\Factories;

use App\Models\TrackKeloladesa;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackKeloladesaFactory extends Factory
{
    protected $model = TrackKeloladesa::class;

    public function definition()
    {
        return [
            'id_device' => $this->faker->unique()->uuid(),
            'kode_desa' => $this->faker->numerify('##########'),
            'versi' => $this->faker->randomElement(['2507.0.0', '2403.0.0', '2402.0.0']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
