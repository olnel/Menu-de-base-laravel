<?php

namespace App\Services;

use App\Repositories\InfosocieteRepository;
use App\Services\Base\BaseImageService;

class InfosocieteService extends BaseImageService
{
    protected array $imageFields = [
        'main' => [
            'field' => 'logo_societe',
            'path_field' => 'logo_societe',
            'thumb_field' => 'thumb_img',
            'path_prefix' => 'img/infosociete',
            'name_prefix' => 'infosociete',
            'create_thumb' => true,
            'max_dimension' => 1920,
            'quality' => 80,
            'format' => 'webp'
        ],
    ];

    protected $repository;
    public function __construct(InfosocieteRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    public function findElement()
    {
        return $this->repository->fetchElement();
    }
}
