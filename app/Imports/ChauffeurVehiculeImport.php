<?php

namespace App\Imports;

use App\Models\Chauffeur;
use App\Models\ChauffeurVehicule;
use App\Models\Remorque;
use App\Models\Vehicule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 

class ChauffeurVehiculeImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $rows = $rows->map(function ($row) {
            return collect($row)->mapWithKeys(function ($value, $key) {
                return [strtolower($key) => $value];
            });
        });

        $rows = $rows->map(function ($row) {
            return $row->mapWithKeys(function ($value, $key) {
                $newKey = str_replace(' ', '_', trim($key));
                if ($newKey === '') {
                    $newKey = '_';
                }
                return [$newKey => $value];
            });
        });

        $rows = $rows->filter(function ($row) {
            return !$row->filter()->isEmpty();
        });

        if ($rows->isEmpty()) {
            return;
        }

        $uniqueRemorques = [];
        $uniqueChauffeurs = [];
        $uniqueVehicules = []; 

        foreach ($rows as $row) {
            $numeroRemorque = trim($row['numero_remorque'] ?? '');
            if (!empty($numeroRemorque)) {
                $uniqueRemorques[$numeroRemorque] = ['numero_remorque' => $numeroRemorque];
            }

            $chauffeurNom = $row['chauffeur'];
            $chauffeurContact =  trim($row['chauffeur_contact']);
            if (!empty($chauffeurNom) && !empty($chauffeurContact)) {
                $uniqueChauffeurs[$chauffeurNom . '|' . $chauffeurContact] = [
                    'nom' => $chauffeurNom,
                    'telephone' => $chauffeurContact
                ];
            }

            $immatriculation = trim($row['numero_camion'] ?? '');
            if (!empty($immatriculation)) {
                $uniqueVehicules[$immatriculation] = ['immatriculation' => $immatriculation];
            }
        }

        if (!empty($uniqueRemorques)) {
            Remorque::upsert(array_values($uniqueRemorques), ['numero_remorque'], ['numero_remorque']);
        }

        $remorquesMap = Remorque::whereIn('numero_remorque', array_keys($uniqueRemorques))
                                ->pluck('id', 'numero_remorque')
                                ->toArray();

        // Log::info('uniqueChauffeurss: ' . json_encode($uniqueChauffeurs));

        if (!empty($uniqueChauffeurs)) {
            Chauffeur::upsert(array_values($uniqueChauffeurs), ['nom', 'telephone'], ['nom', 'telephone']);
        }

        $chauffeursMap = [];
        foreach (Chauffeur::whereIn(DB::raw('CONCAT(nom, "|", telephone)'), array_keys($uniqueChauffeurs))->get() as $chauffeur) {
            $chauffeursMap[$chauffeur->nom . '|' . $chauffeur->telephone] = $chauffeur->id;
        }

        $vehiculesToUpsert = [];
        foreach ($rows as $row) {
            $immatriculation = trim($row['numero_camion'] ?? '');
            $numeroRemorque = trim($row['numero_remorque'] ?? '');

            if (!empty($immatriculation)) {
                $remorqueId = $remorquesMap[$numeroRemorque] ?? null;

                $vehiculesToUpsert[$immatriculation] = [
                    'immatriculation' => $immatriculation,
                    'remorque_id' => $remorqueId,
                ];
            }
        }

        if (!empty($vehiculesToUpsert)) {
            Vehicule::upsert(array_values($vehiculesToUpsert), ['immatriculation'], ['remorque_id']);
        }

        $vehiculesMap = Vehicule::whereIn('immatriculation', array_keys($uniqueVehicules))
                                ->pluck('id', 'immatriculation')
                                ->toArray();

        $chauffeurVehiculeToUpsert = [];
        foreach ($rows as $row) {
            $chauffeurNom = trim($row['chauffeur']) ;
            $chauffeurContact = trim($row['chauffeur_contact']);
            $immatriculation = trim($row['numero_camion'] ?? '');

            $chauffeurId = $chauffeursMap[$chauffeurNom . '|' . $chauffeurContact] ?? null;
            $vehiculeId = $vehiculesMap[$immatriculation] ?? null;

            if ($chauffeurId && $vehiculeId) {
                $chauffeurVehiculeToUpsert[] = [
                    'chauffeur_id' => $chauffeurId,
                    'vehicule_id' => $vehiculeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($chauffeurVehiculeToUpsert)) {
            ChauffeurVehicule::upsert(
                $chauffeurVehiculeToUpsert,
                ['chauffeur_id', 'vehicule_id'], 
                ['created_at', 'updated_at'] 
            );
        }

        Log::info('Importation terminée avec succès.');
    }
}