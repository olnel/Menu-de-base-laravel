<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TresorerieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom_tresorerie' => $this->faker->word() . ' Bank',
            'solde' => $this->faker->randomFloat(2, 1000, 100000),
            'type_tresorerie' => $this->faker->randomElement(['Banque', 'Caisse']),
            'numero_compte' => $this->faker->bankAccountNumber(),
            'bic' => $this->faker->swiftBicNumber(),
            'iban' => $this->faker->iban(),
            'banque_correspondante' => $this->faker->company(),
            'code_swift' => $this->faker->swiftBicNumber(),
            'titulaire_compte' => $this->faker->name(),
        ];
    }
}
