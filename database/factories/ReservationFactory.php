<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date_reservation' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'numero_reservation' => 'RES-' . $this->faker->unique()->numberBetween(1000, 9999),
            'count_numero_reservation' => $this->faker->numberBetween(1, 1000),
            'user_id' => \App\Models\User::factory(),
            'client_id' => \App\Models\Client::factory(),
            'lieu_chargement' => $this->faker->city(),
            'lieu_livraison' => $this->faker->city(),
            'nbr_vehicule' => $this->faker->numberBetween(1, 5),
        ];
    }
}
