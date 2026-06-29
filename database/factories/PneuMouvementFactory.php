<?php

namespace Database\Factories;

use App\Models\PneuMouvement;
use Illuminate\Database\Eloquent\Factories\Factory;

class PneuMouvementFactory extends Factory
{
    protected $model = PneuMouvement::class;

    public function definition(): array
    {
        return [
            'article_id' => \App\Models\Article::factory(),
            'pneu_serie_id' => \App\Models\PneuSerie::factory(),
            'magasin_id' => \App\Models\Magasin::factory(),
            'reference_mvt' => 'PN-MV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'type_mvt' => $this->faker->randomElement(['Entrée', 'Sortie']),
        ];
    }
}
