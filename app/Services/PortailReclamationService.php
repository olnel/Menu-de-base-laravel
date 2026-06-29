<?php

namespace App\Services;

use App\Models\Reclamation;
use App\Models\ReclamationImage;
use App\Repositories\PortailReclamationRepository;
use App\Services\Base\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PortailReclamationService extends BaseService
{
    protected array $relation = ['images'];

    protected array $scope = [
        'filter'          => 'search',
        'filterclient'    => 'client_id',
        'filterstatut'    => 'statut',
        'filterdatestart' => 'start_date',
        'filterdateend'   => 'end_date',
    ];

    public function __construct(
        PortailReclamationRepository $repository,
        private NotificationService  $notificationService,
        private ImageService         $imageService,
    ) {
        parent::__construct($repository);
        $this->repository->setDefaultOrder('created_at', 'desc');
    }

    public function store(int $clientId, array $validated, array $images = []): Reclamation
    {
        return DB::transaction(function () use ($clientId, $validated, $images) {
            $reclamation = $this->repository->create([
                'client_id'          => $clientId,
                'numero_reclamation' => $this->repository->nextNumero(),
                'objet'              => $validated['objet'],
                'categorie'          => $validated['categorie'],
                'description'        => $validated['description'],
                'voyage_id'          => $validated['voyage_id'] ?? null,
                'statut'             => 'en_attente',
            ]);

            foreach ($images as $image) {
                $this->storeImage($reclamation, $image);
            }

            $reclamation->loadMissing('client');
            $this->notificationService->notifyPortailReclamation($reclamation);

            return $reclamation;
        });
    }

    public function getClientVoyages(int $clientId): array
    {
        return $this->repository->getClientVoyages($clientId);
    }

    private function storeImage(Reclamation $reclamation, UploadedFile $file): void
    {
        $prefix = 'img/reclamations/' . $reclamation->id . '/'
            . Str::slug($reclamation->numero_reclamation) . '_' . now()->getTimestampMs();

        $result = $this->imageService->processWithMemorySafety($file, $prefix, [
            'max_dimension' => 1920,
            'quality'       => 80,
            'format'        => 'webp',
            'create_thumb'  => false,
        ]);

        ReclamationImage::create([
            'reclamation_id' => $reclamation->id,
            'chemin'         => $result['main']['path'],
            'nom_original'   => $file->getClientOriginalName(),
        ]);
    }
}
