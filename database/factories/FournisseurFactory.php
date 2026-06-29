<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FournisseurFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom_fournisseur' => $this->faker->company(),
            'adresse_fournisseur' => $this->faker->address(),
            'mail_fournisseur' => $this->faker->unique()->safeEmail(),
            'tel_fournisseur' => $this->faker->phoneNumber(),
            'nif_fournisseur' => $this->faker->numerify('###########'),
            'stat_fournisseur' => $this->faker->numerify('###########'),
            'rcs_fournisseur' => $this->faker->numerify('###########'),
        ];
    }
}
