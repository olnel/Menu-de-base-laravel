<?php

namespace Database\Factories;

use App\Models\Chauffeur;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChauffeurFactory extends Factory
{
    protected $model = Chauffeur::class;

    public function definition(): array
    {
        return [
            'matricule' => $this->faker->unique()->bothify('CH-####'),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'date_naissance' => $this->faker->date('Y-m-d', '-20 years'),
            'CIN' => $this->faker->unique()->numerify('###########'),
            'telephone' => $this->faker->phoneNumber(),
            'adresse' => $this->faker->address(),
        ];
    }
}
