<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleFamille;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'article_famille_id' => ArticleFamille::factory(),
            'reference' => $this->faker->unique()->bothify('ART-####'),
            'designation' => $this->faker->words(3, true),
            'type_article' => $this->faker->randomElement(['Pneu', 'Pièce', 'Accessoire']),
            'marque' => $this->faker->company(),
            'valeur' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
