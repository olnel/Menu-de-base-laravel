<?php

namespace App\Services;

use App\Repositories\RemorqueDocumentRepository;
use App\Services\Base\BaseService;
use App\Utils\ForamatDate;

class RemorqueDocumentService extends BaseService
{
    protected $repository;
    public function __construct(RemorqueDocumentRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
            ->setFilterLabel('type_element');
    }

    public function getDocumentWithDecoded($vehiculeId)
    {
        return $this->repository->getDocumentByVehicule($vehiculeId);
    }
    public function create(array $validated): array
    {
        $validated['date_expiration'] = ForamatDate::normaliserDate($validated['date_expiration']);
        $validated['fichier_jointe'] = json_encode($this->generatePJ($validated['fichier_jointe'],$validated['remorque_id']) );

        return parent::create($validated);
    }

    public function update($model, array $validated): array
    {

        $existingFiles = !empty($validated['existing_files'])
            ? ($validated['existing_files'])
            : [];

        $existingFiles = is_array($existingFiles) ? $existingFiles : [];
        $validated['date_expiration'] = ForamatDate::normaliserDate($validated['date_expiration']);

        // Génération des nouveaux fichiers joints
        $newFiles = isset($validated['fichier_jointe'])
            ? $this->generatePJ($validated['fichier_jointe'], $validated['remorque_id'])
            : [];

        // Fusion des anciens et nouveaux fichiers
        $validated['fichier_jointe'] = json_encode(array_merge($existingFiles, $newFiles));

        return parent::update($model, $validated);
    }


    private function generatePJ(array $pieceJoint , int $vehiculeId): array
    {
        $uploadedPaths = [];
        foreach ($pieceJoint as $file) {
            $extension = $file->getClientOriginalExtension();
            $random = random_int(100000, 999999);
            $filename = $vehiculeId . '_' . $random . '.' . $extension;

            $relativePath = "img/remorque/documents";
            $destinationPath = public_path($relativePath);

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);
            $uploadedPaths[] = "{$relativePath}/{$filename}";
        }

        return $uploadedPaths;
    }


    public function delete($model): array
    {
        // 1. Supprimer les fichiers du disque s’ils existent
        if (!empty($model->fichier_jointe)) {
            $files = json_decode($model->fichier_jointe, true);

            foreach ($files as $filePath) {
                $path = $filePath['src'] ?? $filePath;
                $fullPath = public_path($path);

                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        // 2. Supprimer le modèle de la base
        return parent::delete($model);

    }

    public function planningDocumentRemorqueExpiration(array $filters = []): array
    {
        $today = \Carbon\Carbon::today();

        $results = $this->repository->fetchData(['remorque'], [], [], '','',
            fn($query) => $query->whereNotNull('date_expiration')
        );

        return $results->map(function ($doc) use ($today) {
            $alertDays = 30;
            
            $model = \App\Models\DocumentModel::whereHas('documentType', function($q) use ($doc) {
                $q->where('nom', $doc->nom_document);
            })->whereHas('documentableType', function($q) {
                $q->where('model_class', \App\Models\Remorque::class);
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
            $numeroRemorque = $doc->remorque ? $doc->remorque->numero_remorque : 'Inconnu';

            return [
                'id' => 'rem_' . $doc->id,
                'title' => $numeroRemorque . ' : ' . $doc->nom_document,
                'description' => $doc->description,
                'date' => $expirationDate->format('d-m-Y'),
                'background' => $isExpired ? '#ff4d4f' : '#fadb14', // Red if expired, Yellow if warning
                'text_color' => $isExpired ? '#ffffff' : '#000000',
                'type' => 'document',
                'entity_type' => 'Remorque',
                'nom_document' => $doc->nom_document,
                'entity_name' => $numeroRemorque
            ];
        })->filter()->all();
    }

}
