<?php

namespace Database\Factories;

use App\Models\Akses;
use App\Models\Desa;
use Illuminate\Database\Eloquent\Factories\Factory;

class AksesFactory extends Factory
{
    protected $model = Akses::class;

    public function definition()
    {
        return [
            'desa_id' => Desa::factory(),
            'url_referrer' => $this->faker->url(),
            'request_uri' => '/' . $this->faker->word(),
            'client_ip' => $this->faker->ipv4(),
            'external_ip' => $this->faker->ipv4(),
            'opensid_version' => $this->faker->randomElement(['2507.0.0', '2403.0.0']),
            'tgl' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
