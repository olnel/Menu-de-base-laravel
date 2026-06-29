<?php

namespace App\Services;

use App\Repositories\ChauffeurRepository;
use App\Repositories\ChauffeurDocumentRepository;

use App\Services\Base\BaseImageService;

class ChauffeurService extends BaseImageService
{
    protected $service;
    protected array $relation = ['vehicules', 'documents'];

    protected array $imageFields = [
        'main' => [
            'field' => 'img',
            'path_field' => 'img',
            'thumb_field' => 'thumb_img',
            'path_prefix' => 'img/chauffeur',
            'name_prefix' => 'chauffeur',
            'create_thumb' => true,
            'max_dimension' => 1920,
            'quality' => 80,
            'format' => 'webp'
        ],
    ];

    public function __construct(ChauffeurRepository $chauffeurRepository, ImageService $imageService)
    {
        $this->service = $imageService;
        parent::__construct($chauffeurRepository, $this->service);
    }
    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('nom');
    }

    /**
     * Récupère un chauffeur avec les relations définies dans $relation
     */
    public function find($id)
    {
        return $this->repository->getModel()->with($this->relation)->findOrFail($id);
    }
    public function getDocumentsByChauffeur(int $chauffeurId)
    {
            // Use the repository method to get documents for the specific chauffeur
            return $this->repository->getDocumentByChauffeur($chauffeurId);
    }
    public function getMappedChauffeurs() {
        return $this->getAll([])->map(function ($chauffeur) {
            return [
                'value'              => $chauffeur->id,
                'label'              => $chauffeur->nom . ' ' . $chauffeur->prenom,
                'is_aide_chauffeur'  => (bool)$chauffeur->is_aide_chauffeur,
                'vehicule_id'        => $chauffeur->vehicule_id,
                'parent_chauffeur_id'=> $chauffeur->parent_chauffeur_id,
            ];
        });
    }
}
