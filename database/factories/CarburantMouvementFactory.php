<?php

namespace Database\Factories;

use App\Models\CarburantMouvement;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarburantMouvementFactory extends Factory
{
    protected $model = \App\Models\CarburantMouvement::class;

    public function definition(): array
    {
        return [
            'carburant_card_id' => \App\Models\CarburantCard::factory(),
            'type' => $this->faker->randomElement(['achat_carte', 'achat_espece', 'recharge', 'ajustement']),
            'date_mvmt' => $this->faker->date(),
            'date_heure_enregistrement' => $this->faker->dateTime('now'),
            'vehicule_id' => \App\Models\Vehicule::factory(),
            'chauffeur_id' => \App\Models\Chauffeur::factory(),
            'montant' => $this->faker->randomFloat(2, 10, 500),
            'user_id' => \App\Models\User::factory(),
            'reference_mvmt' => 'MVMT-' . $this->faker->unique()->numberBetween(10000, 99999),
        ];
    }
}
