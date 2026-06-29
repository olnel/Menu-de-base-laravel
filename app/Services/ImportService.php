<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

use App\Imports\DynamicImport;
use App\Imports\ChauffeurImportStandard;
use App\Imports\ChauffeurVehiculeImport;
use App\Imports\VehiculeImportStandard;

class ImportService
{
    /**
     * Effectue un import générique avec DynamicImport.
     */
    public function importDynamic(array $validated): void
    {
        $modelClass = $validated['model'];
        $this->validateModelClass($modelClass);

        Excel::import(
            new DynamicImport($validated['columns'], $modelClass),
            $validated['file']
        );
    }

    /**
     * Import spécifique Chauffeur + relation véhicule.
     */
    public function importChauffeurVehicule(UploadedFile $file): void
    {
        Excel::import(new ChauffeurVehiculeImport, $file);
    }

    /**
     * Import spécifique Chauffeurs seuls.
     */
    public function importChauffeur(array $validated): void
    {
        $modelClass = $validated['model'];
        $this->validateModelClass($modelClass);

        Excel::import(
            new ChauffeurImportStandard($validated['columns'], $modelClass),
            $validated['file']
        );
    }

    /**
     * Import spécifique Véhicules + remorque.
     */
    public function importVehicule(array $validated): void
    {
        $modelClass = $validated['model'];
        $this->validateModelClass($modelClass);

        Excel::import(
            new VehiculeImportStandard($validated['columns'], $modelClass),
            $validated['file']
        );
    }

    /**
     * Vérifie que la classe donnée est bien un Model Eloquent valide.
     */
    private function validateModelClass(string $modelClass): void
    {
        if (!class_exists($modelClass) || !is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
            throw ValidationException::withMessages([
                'model' => 'Modèle invalide fourni.'
            ]);
        }
    }
}
