<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero' => 'CLT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'nom_client' => $this->faker->company(),
            'adresse_client' => $this->faker->address(),
            'mail_client' => $this->faker->unique()->safeEmail(),
            'tel_client' => $this->faker->phoneNumber(),
            'nif_client' => $this->faker->numerify('###########'),
            'stat_client' => $this->faker->numerify('###########'),
            'rcs_client' => $this->faker->numerify('###########'),
        ];
    }
}
