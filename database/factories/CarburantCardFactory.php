<?php

namespace Database\Factories;

use App\Models\CarburantCard;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarburantCardFactory extends Factory
{
    protected $model = CarburantCard::class;

    public function definition(): array
    {
        return [
            'numero_carte' => $this->faker->unique()->numerify('CARD-####-####'),
            'solde' => $this->faker->randomFloat(2, 0, 10000),
            'active' => true,
        ];
    }
}
