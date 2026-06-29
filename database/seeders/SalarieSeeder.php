<?php

namespace Database\Seeders;

use App\Models\Salarie;
use App\Models\TypeSalarie;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SalarieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = TypeSalarie::all();
        if ($types->isEmpty()) {
            $this->call(TypeSalarieSeeder::class);
            $types = TypeSalarie::all();
        }

        $noms = [
            'RAKOTO', 'RABE', 'ANDRIA', 'RANDRIA', 'RAZAFY', 'RAVALO', 'RASOA', 'RAMA', 'RAHERY', 'RABARY',
            'RATSIMBA', 'RAJOEL', 'RAMONJA', 'RAKOTONIAINA', 'ANDRIANINA', 'RASOLOFONIRINA', 'RANDRIANARIVELO',
            'RAKOTOMALALA', 'RAZAFIMAHATRATRA', 'RABEMANANJARA', 'ANDRIANASOLO', 'RANDRIAMANANTENA', 'RAKOTOARISOA',
            'RASOLONIAINA', 'RAMANITRA', 'RAKOTOBE', 'ANDRIAMAHANINA', 'RANDRIAMAMONJY', 'RAVALISON', 'RASOANANDRASANA'
        ];

        $prenoms = [
            'Jean', 'Marie', 'Pierre', 'Luc', 'Paul', 'Michel', 'Faly', 'Lova', 'Hery', 'Tiana',
            'Nirina', 'Mamy', 'Solo', 'Rado', 'Tina', 'Zaka', 'Njaka', 'Sitraka', 'Tahina', 'Fitia',
            'Iary', 'Aina', 'Vero', 'Niry', 'Hasina', 'Fenitra', 'Tojo', 'Andry', 'Eric', 'Alice'
        ];

        $villes = ['Antananarivo', 'Toamasina', 'Antsirabe', 'Mahajanga', 'Fianarantsoa', 'Toliara'];
        
        for ($i = 0; $i < 30; $i++) {
            $count = $i + 1;
            $type = $types->random();
            $sexe = (rand(0, 1) == 0) ? 'M' : 'F';
            $nom = $noms[$i % count($noms)];
            $prenom = $prenoms[$i % count($prenoms)];
            
            Salarie::updateOrCreate(
                ['matricule' => sprintf('SAL-%03d', $count)],
                [
                    'count_matricule' => $count,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'sexe' => $sexe,
                    'date_naissance' => Carbon::now()->subYears(rand(20, 50))->subDays(rand(0, 365))->toDateString(),
                    'lieu_naissance' => $villes[rand(0, 5)],
                    'nationalite' => 'Malgache',
                    'cin' => rand(100, 999) . rand(100, 999) . rand(100, 999) . rand(100, 999),
                    'date_cin' => Carbon::now()->subYears(rand(1, 10))->toDateString(),
                    'lieu_cin' => $villes[rand(0, 5)],
                    'telephone' => '034' . rand(10, 99) . rand(100, 999) . rand(10, 99),
                    'email' => strtolower($prenom . '.' . $nom) . $count . '@exemple.mg',
                    'adresse' => 'Lot ' . rand(100, 900) . ' Villa ' . $prenom . ', ' . $villes[rand(0, 5)],
                    'date_embauche' => Carbon::now()->subMonths(rand(1, 60))->toDateString(),
                    'statut' => 'actif',
                    'type_salarie_id' => $type->id,
                    'observation' => 'Salarié de test généré automatiquement.',
                ]
            );
        }
    }
}
