<?php

namespace Database\Factories;

use App\Models\PneuSerie;
use Illuminate\Database\Eloquent\Factories\Factory;

class PneuSerieFactory extends Factory
{
    protected $model = PneuSerie::class;

    public function definition(): array
    {
        return [
            'numero_serie' => $this->faker->unique()->bothify('PNEU-##########'),
            'etat' => $this->faker->randomElement(['Neuf', 'Usé', 'Rechapé']),
            'article_id' => \App\Models\Article::factory(),
            'vehicule_id' => \App\Models\Vehicule::factory(),
        ];
    }
}
