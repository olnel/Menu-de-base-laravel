<?php

namespace Database\Seeders;

use App\Models\InfoSociete;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InfoSocieteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InfoSociete::insert([
            [
                'nom_societe' => 'Société de Test',
                'adresse_societe' => '123 Rue de la Test',
                'telephone_societe' => '0123456789',
                'email_societe' => 'societe@gmail.com',
                'nif_societe' => '123456789',
                'rcs_societe' => '123 456 789',
                'stat_societe' => 'SARL',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
