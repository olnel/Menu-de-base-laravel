<?php

namespace App\Imports;

use App\Models\Chauffeur;
use App\Models\ChauffeurVehicule;
use App\Models\Vehicule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ChauffeurImportStandard implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $columns;
    protected $modelClass;

    public function __construct(array $columns, string $modelClass)
    {
        $this->columns = $columns;
        $this->modelClass = $modelClass;
    }

    public function collection(Collection $rows)
    {
        $modelClass = $this->modelClass;

        // Pré-chargement des chauffeurs existants
        $chauffeursByMatricule = Chauffeur::whereNotNull('matricule')->pluck('id', 'matricule')->toArray();
        $chauffeursByCin = Chauffeur::whereNotNull('CIN')->pluck('id', 'CIN')->toArray();

        // Pré-chargement des véhicules
        $vehiculesByImmat = Vehicule::pluck('id', 'immatriculation')->toArray();

        DB::transaction(function () use ($rows, $chauffeursByMatricule, $chauffeursByCin, $vehiculesByImmat, $modelClass) {
            foreach ($rows as $row) {
                if ($row->filter()->isEmpty()) continue;

                $cin = trim($row['cin'] ?? '');
                $matricule = trim($row['matricule'] ?? '');

                $chauffeurData = [
                    'nom' => trim($row['nom_chauffeur'] ?? 'Inconnu'),
                    'prenom' => trim($row['prenom_chauffeur'] ?? ''),
                    'CIN' => $cin ?: null,
                    'matricule' => $matricule ?: null,
                ];

                foreach ($this->columns as $column) {
                    if (!in_array($column, ['nom', 'prenom', 'CIN', 'matricule']) && isset($row[$column])) {
                        if ($column === 'date_naissance' && !empty($row[$column])) {
                            $chauffeurData[$column] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$column])->format('Y-m-d');
                        } else {
                            $chauffeurData[$column] = trim($row[$column]);
                        }
                    }
                }

                // Recherche en mémoire
                if ($matricule && isset($chauffeursByMatricule[$matricule])) {
                    $chauffeur =  $modelClass::find($chauffeursByMatricule[$matricule]);
                    $chauffeur->update($chauffeurData);
                } elseif ($cin && isset($chauffeursByCin[$cin])) {
                    $chauffeur =  $modelClass::find($chauffeursByCin[$cin]);
                    $chauffeur->update($chauffeurData);
                } else {
                    $chauffeur =  $modelClass::create($chauffeurData);
                    if ($matricule) $chauffeursByMatricule[$matricule] = $chauffeur->id;
                    elseif ($cin) $chauffeursByCin[$cin] = $chauffeur->id;
                }

                // Véhicule
                $immat = trim($row['numero_camion'] ?? '');
                $vehiculeId = $vehiculesByImmat[$immat] ?? null;

                if (!$vehiculeId && $immat) {
                    $vehicule = Vehicule::create(['immatriculation' => $immat]);
                    $vehiculeId = $vehicule->id;
                    $vehiculesByImmat[$immat] = $vehiculeId;
                }

                if ($vehiculeId && $chauffeur) {
                    ChauffeurVehicule::firstOrCreate([
                        'chauffeur_id' => $chauffeur->id,
                        'vehicule_id' => $vehiculeId,
                    ]);
                }
            }
        });
    }

    // Traitement par paquet de lignes (chunk)
    public function chunkSize(): int
    {
        return 1000; // Par exemple, 1000 lignes à la fois
    }

    // Traitement par paquet d'insertion
    public function batchSize(): int
    {
        return 500;
    }
}
