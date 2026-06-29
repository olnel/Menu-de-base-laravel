<?php

namespace Database\Seeders;

use App\Models\TypeSalarie;
use Illuminate\Database\Seeder;

class TypeSalarieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['libelle' => 'Chauffeur', 'description' => 'Personnel de conduite des véhicules'],
            ['libelle' => 'Aide Chauffeur', 'description' => 'Assistant du chauffeur lors des voyages'],
        ];

        foreach ($types as $type) {
            TypeSalarie::updateOrCreate(
                ['libelle' => $type['libelle']],
                ['description' => $type['description']]
            );
        }
    }
}
