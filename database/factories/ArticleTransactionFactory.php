<?php

namespace Database\Factories;

use App\Models\ArticleTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleTransactionFactory extends Factory
{
    protected $model = ArticleTransaction::class;

    public function definition(): array
    {
        return [
            'reference_mouvement' => 'TR-' . $this->faker->unique()->numberBetween(10000, 99999),
            'date_transaction' => $this->faker->date(),
            'date_heure_enregistrement' => $this->faker->dateTime('now'),
            'type_mvt' => $this->faker->randomElement(['Entrée', 'Sortie', 'Transfert']),
            'magasin_id' => \App\Models\Magasin::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
