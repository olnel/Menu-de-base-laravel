<?php

namespace Database\Factories;

use App\Models\Magasin;
use Illuminate\Database\Eloquent\Factories\Factory;

class MagasinFactory extends Factory
{
    protected $model = Magasin::class;

    public function definition(): array
    {
        return [
            'nom_magasin' => $this->faker->company() . ' Warehouse',
        ];
    }
}
