<?php

namespace Database\Factories;

use App\Models\DevisClient;
use Illuminate\Database\Eloquent\Factories\Factory;

class DevisClientFactory extends Factory
{
    protected $model = DevisClient::class;

    public function definition(): array
    {
        return [
            'client_id' => \App\Models\Client::factory(),
            'user_id' => \App\Models\User::factory(),
            'date_devis' => $this->faker->date(),
            'validite_devis' => $this->faker->date(),
            'montant_total' => $this->faker->randomFloat(2, 500, 10000),
            'numero_devis' => 'DEV-' . $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}
