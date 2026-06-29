<?php

namespace Database\Factories;

use App\Models\ArticleApprovisionnement;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleApprovisionnementFactory extends Factory
{
    protected $model = ArticleApprovisionnement::class;

    public function definition(): array
    {
        return [
            'date_appro' => $this->faker->date(),
            'date_echeance' => $this->faker->date(),
            'date_heure_enregistrement' => $this->faker->dateTime('now'),
            'fournisseur_id' => \App\Models\Fournisseur::factory(),
            'magasin_id' => \App\Models\Magasin::factory(),
            'user_id' => \App\Models\User::factory(),
            'boncommande_fournisseur_id' => \App\Models\BoncommandeFournisseur::factory(),
            'numero_bon_commande' => 'BC-' . $this->faker->unique()->numberBetween(1000, 9999),
            'numero_bon_livraison' => 'BL-' . $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}
