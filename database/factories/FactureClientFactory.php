<?php

namespace Database\Factories;

use App\Models\FactureClient;
use Illuminate\Database\Eloquent\Factories\Factory;

class FactureClientFactory extends Factory
{
    protected $model = FactureClient::class;

    public function definition(): array
    {
        return [
            'numero_facture' => 'FAC-' . $this->faker->unique()->numberBetween(1000, 9999),
            'date_facture' => $this->faker->date(),
            'montant_ttc' => $this->faker->randomFloat(2, 1000, 20000),
            'client_id' => \App\Models\Client::factory(),
            'statut_facture' => $this->faker->randomElement(['Brouillon', 'Validée', 'Payée']),
            'taux_tva' => 20,
        ];
    }
}
