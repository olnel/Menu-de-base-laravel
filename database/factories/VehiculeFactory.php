<?php

namespace Database\Factories;

use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculeFactory extends Factory
{
    protected $model = Vehicule::class;

    public function definition(): array
    {
        return [
            'immatriculation' => $this->faker->unique()->bothify('#### ???'),
            'marque' => $this->faker->randomElement(['Renault', 'Mercedes', 'Scania', 'Volvo', 'MAN']),
            'modele' => $this->faker->word(),
            'num_chassis' => $this->faker->unique()->bothify('VIN-#################'),
            'couleur' => $this->faker->safeColorName(),
            'num_carte_grise' => $this->faker->unique()->bothify('CG-######'),
            'nbre_porte' => $this->faker->randomElement([2, 4]),
            'valeur_initial' => $this->faker->randomFloat(2, 20000, 150000),
            'description' => $this->faker->sentence(),
        ];
    }
}
