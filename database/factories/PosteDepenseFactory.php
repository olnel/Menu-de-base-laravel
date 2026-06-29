<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PosteDepenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'libelle' => $this->faker->words(3, true),
        ];
    }
}
