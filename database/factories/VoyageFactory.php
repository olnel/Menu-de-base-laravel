<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VoyageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero_voyage' => 'VOY-' . $this->faker->unique()->numberBetween(1000, 9999),
            'count_numero_voyage' => $this->faker->numberBetween(1, 1000),
            'reservation_id' => \App\Models\Reservation::factory(),
            'vehicule_id' => \App\Models\Vehicule::factory(),
            'chauffeur_id' => \App\Models\Chauffeur::factory(),
            'remorque_id' => \App\Models\Remorque::factory(),
            'destination' => $this->faker->city(),
            'depart' => $this->faker->city(),
            'type_trajet' => $this->faker->randomElement(['National', 'International']),
            'etat_reception' => $this->faker->randomElement(['En attente', 'Reçu']),
            'tarif' => $this->faker->randomFloat(2, 500, 5000),
            'nbr_jour' => $this->faker->numberBetween(1, 10),
            'mobilisation' => $this->faker->randomFloat(2, 0, 500),
            'montant' => $this->faker->randomFloat(2, 1000, 10000),
            'date_voyage' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'montant_ht' => $this->faker->randomFloat(2, 1000, 10000),
            'tarif_ht' => $this->faker->randomFloat(2, 500, 5000),
            'tarif_ttc' => $this->faker->randomFloat(2, 600, 6000),
            'valeur_tva' => 20,
            'montant_tva' => $this->faker->randomFloat(2, 100, 1000),
            'etat_vehicule_avant' => 'Bon état',
            'etat_vehicule_apres' => 'Bon état',
        ];
    }
}
