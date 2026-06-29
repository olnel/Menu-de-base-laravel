<?php

namespace App\Services;

use App\Models\DocumentChauffeur;
use App\Repositories\ChauffeurDocumentRepository; // Corrected import
use App\Services\Base\BaseImageService;
use App\Services\Base\BaseService;
use App\Utils\ForamatDate;
use Carbon\Carbon;

class ChauffeurDocumentService extends BaseService
{
    protected $repository;
    public function __construct(ChauffeurDocumentRepository $repository) // Use ChauffeurDocumentRepository
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {

    }

    // Add this new method to fetch documents by chauffeur ID
    public function getDocumentsByChauffeur(int $chauffeurId)
    {
        // Use the repository method to get documents for the specific chauffeur
        return $this->repository->getDocumentByChauffeur($chauffeurId);
    }

    public function create(array $validated): array
    {
        if (isset($validated['fichier_jointe'])) {

            $validated['fichier_jointe'] = json_encode($this->generatePJ($validated['fichier_jointe'], $validated['chauffeur_id']));
        }
        return parent::create($validated);
    }

    public function update($documentId, array $validated): array
    {
        // 1. Récupérer le modèle du document existant pour comparer les fichiers
        $model = DocumentChauffeur::find($documentId);

        // Décoder les fichiers existants
        $existingFiles = !empty($validated['existing_files'])
            ? json_decode($validated['existing_files'], true)
            : [];

        // Génération des nouveaux fichiers joints
        $newFiles = isset($validated['fichier_jointe'])
            ? $this->generatePJ($validated['fichier_jointe'], $validated['chauffeur_id'])
            : [];

        // Fusion des anciens et nouveaux fichiers
        $validated['fichier_jointe'] = json_encode(array_merge($existingFiles, $newFiles));

        return parent::update($model, $validated);
    }

    private function generatePJ(array $pieceJoint, int $chauffeurId): array
    {
        $uploadedPaths = [];
        foreach ($pieceJoint as $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $extension = $file->getClientOriginalExtension();
                $random = random_int(100000, 999999);
                $filename = $chauffeurId . '_' . $random . '.' . $extension;
                $relativePath = "img/chauffeur/documents";
                $destinationPath = public_path($relativePath);

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $uploadedPaths[] = "{$relativePath}/{$filename}";
            }
        }

        return $uploadedPaths;
    }


    public function delete($id): array
    {
        $model = DocumentChauffeur::find($id);
        // 1. Supprimer les fichiers du disque s'ils existent
        $files = $model->fichier_jointe; // Access as array directly

        if (!empty($files) && is_array($files)) {
            foreach ($files as $filePath) {
                // Assuming filePath is just the path string
                $fullPath = public_path($filePath);

                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        // 2. Supprimer le modèle de la base
        return parent::delete($model);

    }


    public function planningDocumentChauffeurExpiration(array $filters = []): array
    {
        $today = \Carbon\Carbon::today();

        $results = $this->repository->fetchData(['chauffeur'],[],[],'','',
            fn($query) => $query->whereNotNull('date_expiration')
        );

        return $results->map(function ($doc) use ($today) {
            $alertDays = 30;
            
            $model = \App\Models\DocumentModel::whereHas('documentType', function($q) use ($doc) {
                $q->where('nom', $doc->nom_document);
            })->whereHas('documentableType', function($q) {
                $q->where('model_class', \App\Models\Chauffeur::class);
            })->first();

            if ($model) {
                $alertDays = $model->alert_days;
            }

            $expirationDate = \Carbon\Carbon::parse($doc->date_expiration);
            $diff = $today->diffInDays($expirationDate, false);

            if ($diff > $alertDays) {
                return null;
            }

            $isExpired = $today->greaterThan($expirationDate);
            $agentName = $doc->chauffeur ? (strtoupper($doc->chauffeur->nom) . ' ' . ucwords($doc->chauffeur->prenom)) : 'Inconnu';

            return [
                'id' => 'chauf_' . $doc->id,
                'title' => $agentName . ' : ' . $doc->nom_document,
                'description' => null,
                'date' => $expirationDate->format('d-m-Y'),
                'background' => $isExpired ? '#ff4d4f' : '#fadb14', // Red if expired, Yellow if warning
                'text_color' => $isExpired ? '#ffffff' : '#000000',
                'type' => 'document',
                'entity_type' => 'Agent',
                'nom_document' => $doc->nom_document,
                'entity_name' => $agentName
            ];
        })->filter()->all();
    }

}
