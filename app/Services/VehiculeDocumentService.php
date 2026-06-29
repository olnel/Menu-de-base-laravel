<?php

namespace App\Services;

use App\Repositories\VehiculeDocumentRepository;
use App\Repositories\VehiculeRepository;
use App\Services\Base\BaseImageService;
use App\Services\Base\BaseService;
use App\Utils\ForamatDate;

class VehiculeDocumentService extends BaseService
{
    protected $repository;
    private VehiculeRepository $vehiculeRepository;
    private VehiculeDocumentRepository $vehiculeDocumentRepository;
    protected array $scope =['filter' => 'search',  'filterdatestart' => 'start_date', 'filterdateend' => 'end_date', 'filtervehicule' => 'vehicule_id'];
    public function __construct(VehiculeDocumentRepository $repository, VehiculeRepository $vehiculeRepository)
    {
        $this->repository = $repository;
        $this->vehiculeRepository = $vehiculeRepository;
        $this->vehiculeDocumentRepository = $this->repository;
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
        $validated['vehicule_id'] = $this->findVehicule($validated['immatriculation'])->id;
        $validated['fichier_jointe'] = json_encode($this->generatePJ($validated['fichier_jointe'],$validated['vehicule_id']) );

        return parent::create($validated);
    }

    private function findVehicule(string $immatriculation)
    {
        return $this->vehiculeRepository->findElement(['immatriculation' => $immatriculation]);

    }

    public function update($model, array $validated): array
    {

        $existingFiles = !empty($validated['existing_files'])
            ? ($validated['existing_files'])
            : [];

        $existingFiles = is_array($existingFiles) ? $existingFiles : [];
        $validated['date_expiration'] = ForamatDate::normaliserDate($validated['date_expiration']);

        // Génération des nouveaux fichiers joints
        $validated['vehicule_id'] = $this->findVehicule($validated['immatriculation'])->id;
        $newFiles = isset($validated['fichier_jointe'])
            ? $this->generatePJ($validated['fichier_jointe'], $validated['vehicule_id'])
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

            $relativePath = "img/vehicule/documents";
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

    public function planningDocumentVehiculeExpiration(array $filters = []): array
    {
        $today = \Carbon\Carbon::today();
        
        // On récupère tous les documents avec une date d'expiration
        $results = $this->repository->fetchData(['vehicule'], [], [], '','',
            fn($query) => $query->whereNotNull('date_expiration')
        );

        // On filtre manuellement pour appliquer la logique d'alerte
        // Pour les anciens documents, on essaie de trouver le DocumentModel correspondant
        // ou on utilise une valeur par défaut de 30 jours
        return $results->map(function ($doc) use ($today) {
            $alertDays = 30; // Défaut
            
            // Tentative de trouver le modèle pour avoir les alert_days configurés
            $model = \App\Models\DocumentModel::whereHas('documentType', function($q) use ($doc) {
                $q->where('nom', $doc->nom_document);
            })->whereHas('documentableType', function($q) {
                $q->where('model_class', \App\Models\Vehicule::class);
            })->first();

            if ($model) {
                $alertDays = $model->alert_days;
            }

            $expirationDate = \Carbon\Carbon::parse($doc->date_expiration);
            $diff = $today->diffInDays($expirationDate, false);

            // On n'affiche que si on est dans la période d'alerte
            // Si c'est déjà expiré (diff < 0), on continue d'afficher tant que ce n'est pas renouvelé
            if ($diff > $alertDays) {
                return null;
            }

            $isExpired = $today->greaterThan($expirationDate);
            $immatriculation = $doc->vehicule ? $doc->vehicule->immatriculation : 'Inconnu';

            return [
                'id' => 'veh_' . $doc->id,
                'title' => $immatriculation . ' : ' . $doc->nom_document,
                'description' => $doc->description,
                'date' => $expirationDate->format('d-m-Y'),
                'background' => $isExpired ? '#ff4d4f' : '#fadb14', // Red if expired, Yellow if warning
                'text_color' => $isExpired ? '#ffffff' : '#000000',
                'type' => 'document',
                'entity_type' => 'Tracteur',
                'nom_document' => $doc->nom_document,
                'entity_name' => $immatriculation
            ];
        })->filter()->all();
    }
}
