<?php

namespace Database\Factories;

use App\Models\CarburantTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarburantTransactionFactory extends Factory
{
    protected $model = CarburantTransaction::class;

    public function definition(): array
    {
        return [
            'reference' => 'TRNS-' . $this->faker->unique()->numberBetween(10000, 99999),
            'carburant_card_id' => \App\Models\CarburantCard::factory(),
            'type' => $this->faker->randomElement(['achat_carte', 'achat_espece']),
            'date_transaction' => $this->faker->date(),
            'date_heure_enregistrement' => $this->faker->dateTime('now'),
            'vehicule_id' => \App\Models\Vehicule::factory(),
            'chauffeur_id' => \App\Models\Chauffeur::factory(),
            'user_id' => \App\Models\User::factory(),
            'montant' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}
