<?php

namespace Database\Factories;

use App\Models\FactureClientReglement;
use Illuminate\Database\Eloquent\Factories\Factory;

class FactureClientReglementFactory extends Factory
{
    protected $model = FactureClientReglement::class;

    public function definition(): array
    {
        return [
            'facture_client_id' => \App\Models\FactureClient::factory(),
            'tresorerie_id' => \App\Models\Tresorerie::factory(),
            'montant_reglement' => $this->faker->randomFloat(2, 100, 5000),
            'date_reglement' => $this->faker->date(),
        ];
    }
}
