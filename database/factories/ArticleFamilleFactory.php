<?php

namespace Database\Factories;

use App\Models\ArticleFamille;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFamilleFactory extends Factory
{
    protected $model = ArticleFamille::class;

    public function definition(): array
    {
        return [
            'nom_famille_article' => $this->faker->word(),
        ];
    }
}
