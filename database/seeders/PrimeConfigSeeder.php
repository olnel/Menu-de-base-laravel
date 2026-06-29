<?php

namespace Database\Seeders;

use App\Models\PrimeConfig;
use App\Models\TypeSalarie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrimeConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider la table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PrimeConfig::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Récupérer les types
        $chauffeurType = TypeSalarie::where('libelle', 'Chauffeur')->first();
        $aideType = TypeSalarie::where('libelle', 'Aide Chauffeur')->first();
        $comptableType = TypeSalarie::where('libelle', 'Comptable')->first();

        $primes = [
            [
                'libelle' => 'Prime de Voyage (Chauffeur)',
                'montant' => 15000,
                'type_salarie_id' => $chauffeurType?->id,
                'is_global' => false,
                'is_per_voyage' => true,
            ],
            [
                'libelle' => 'Prime de Voyage (Aide)',
                'montant' => 7500,
                'type_salarie_id' => $aideType?->id,
                'is_global' => false,
                'is_per_voyage' => true,
            ],
            [
                'libelle' => 'Indemnité de Logement',
                'montant' => 50000,
                'type_salarie_id' => null,
                'is_global' => true,
                'is_per_voyage' => false,
            ],
            [
                'libelle' => 'Prime de Responsabilité',
                'montant' => 100000,
                'type_salarie_id' => $comptableType?->id,
                'is_global' => false,
                'is_per_voyage' => false,
            ],
            [
                'libelle' => 'Prime Panier',
                'montant' => 5000,
                'type_salarie_id' => null,
                'is_global' => true,
                'is_per_voyage' => false,
            ],
        ];

        foreach ($primes as $prime) {
            PrimeConfig::create($prime);
        }
    }
}
