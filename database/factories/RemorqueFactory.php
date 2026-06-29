<?php

namespace Database\Factories;

use App\Models\Remorque;
use Illuminate\Database\Eloquent\Factories\Factory;

class RemorqueFactory extends Factory
{
    protected $model = Remorque::class;

    public function definition(): array
    {
        return [
            'numero_remorque' => $this->faker->unique()->bothify('REM-####'),
            'modele_remorque' => $this->faker->word(),
            'marque_remorque' => $this->faker->company(),
        ];
    }
}
