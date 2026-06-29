<?php

namespace App\Services;

use App\Repositories\RemorquePhotoRepository;
use App\Services\Base\BaseImageService;

class RemorquePhotoService extends BaseImageService
{
    protected array $imageFields= [
        'main' => [
            'field' => 'liste_image',
            'path_field' => 'liste_image',
            'thumb_field' => null,
            'path_prefix' => 'img/remorque',
            'name_prefix' => 'remorque',
            'create_thumb' => false,
            'max_dimension' => 1920,
            'quality' => 80,
            'format' => 'webp',
            'multiple' => true
        ]
    ];

    protected $repository;

    protected array $scope = ['filter' => 'search', 'Typeelement' => 'type_element', 'filterdatestart' => 'start_date', 'filterdateend' => 'end_date'];

    public function __construct(RemorquePhotoRepository $repository, ImageService $imageService)
    {
        $this->repository = $repository;
        parent::__construct($repository, $imageService);
    }

    public function getPhotosWithDecodedImages(int $vehiculeId)
    {
        return $this->repository->getElementsByVehicule($vehiculeId);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
            ->setFilterLabel('type_element');
    }


    public function update($model, array $validated): array
    {

        $existingFiles = !empty($validated['existing_images'])
            ? ($validated['existing_images'])
            : null;

        $validated['liste_image'] = (array_merge($existingFiles, $validated['liste_image'] ?? []));

        return parent::update($model, $validated);
    }

}
