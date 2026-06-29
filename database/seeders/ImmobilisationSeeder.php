<?php

namespace Database\Seeders;

use App\Models\Immobilisation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ImmobilisationSeeder extends Seeder
{
    public function run()
    {
        Immobilisation::insert([
            [
                'libelle' => 'immobilisation',
                'valeur' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
