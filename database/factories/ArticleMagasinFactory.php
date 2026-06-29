<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleMagasin;
use App\Models\Magasin;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleMagasinFactory extends Factory
{
    protected $model = ArticleMagasin::class;

    public function definition(): array
    {
        return [
            'article_id' => Article::factory(),
            'magasin_id' => Magasin::factory(),
            'stock' => $this->faker->numberBetween(0, 100),
        ];
    }
}
