<?php

namespace Database\Factories;

use App\Models\BoncommandeFournisseur;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoncommandeFournisseurFactory extends Factory
{
    protected $model = BoncommandeFournisseur::class;

    public function definition(): array
    {
        return [
            'numero_bon_commande' => 'BC-' . $this->faker->unique()->numberBetween(1000, 9999),
            'count_numero_commande' => $this->faker->numberBetween(1, 1000),
            'date_boncommande' => $this->faker->date(),
            'date_heure_enregistrement' => $this->faker->dateTime('now'),
            'fournisseur_id' => \App\Models\Fournisseur::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
