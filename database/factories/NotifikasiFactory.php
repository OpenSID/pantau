<?php

namespace Database\Factories;

use App\Models\Notifikasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotifikasiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notifikasi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'judul' => $this->faker->sentence,
            'isi' => $this->faker->paragraph,
            'aktif' => $this->faker->randomElement([0, 1]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}