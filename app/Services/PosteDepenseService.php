<?php

namespace App\Services;

use App\Repositories\PosteDepenseRepository;
use App\Services\Base\BaseService;
use FontLib\Table\Type\post;

class PosteDepenseService extends BaseService
{
    protected $repository;
    public function __construct(PosteDepenseRepository $posteDepenseRepository)
    {
        $this->repository = $posteDepenseRepository;
        parent::__construct($posteDepenseRepository);
    }

    /**
     * fonction permettant de créer ou modifier un poste de dépense
     * @param string
     * @return void
     */
    public function createOrEdit($poste_depense)
    {
        $posteDepense = $this->repository->findElement(['libelle' => $poste_depense]);

        //si poste_depense est null donc on crée si non on modifie
        if (!empty($posteDepense)){
                parent::update($posteDepense, ['libelle' => $poste_depense]);
        } else {
            $this->repository->create([
                'libelle' => $poste_depense,
            ]);
        }
    }
}
