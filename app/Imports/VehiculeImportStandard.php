<?php

namespace App\Imports;

use App\Models\Chauffeur;
use App\Models\ChauffeurVehicule;
use App\Models\Remorque;
use App\Models\Vehicule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB; 
use Maatwebsite\Excel\Concerns\WithHeadingRow; 
use Illuminate\Support\Facades\Log; 

class VehiculeImportStandard implements ToCollection, withHeadingRow
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

        foreach ($rows as $row) {
            if ($row->filter()->isEmpty()) continue;

            // if (empty($row['numero_camion'])) {
            //     continue;
            // }
            
            $data = [];

            $remorque = null;
            if (!empty($row['numero_remorque'])) {
                $remorque = Remorque::firstOrCreate([
                    'numero_remorque' => trim($row['numero_remorque']),
                ]);
            }

            foreach ($this->columns as $column) {
                if ($column === 'remorque_id') {
                    $data['remorque_id'] = $remorque?->id;
                } elseif (isset($row[$column])) {
                    $data[$column] = trim($row[$column]);
                }
            }

            if (!empty($data)) {
                $vehicule = $modelClass::where('immatriculation', trim($row['numero_camion']))->first();

                if ($vehicule) {
                    $vehicule->update($data);
                } else {
                    $modelClass::firstOrCreate(
                        ['immatriculation' => trim($row['numero_camion'])],
                        $data
                    );
                }
            }
        }
    }

}