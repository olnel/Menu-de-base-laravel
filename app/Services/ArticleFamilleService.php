<?php

namespace App\Services;

use App\Repositories\ArticleFamilleRepository;
use App\Services\Base\BaseService;
use Exception;

class ArticleFamilleService extends BaseService
{
    protected $repository;
    public function __construct(ArticleFamilleRepository $articleFamilleRepository)
    {
        $this->repository = $articleFamilleRepository;
        parent::__construct($articleFamilleRepository);
    }
    // Your service methods go here
    protected function initializeFilters(): void
    {
        $this->setFilterLabel('nom_famille_article')->setFilterValue('id');
    }

    /**
     * fonction permet d'inserer famille article et fait modificaton si la famille article n'est pas encore enregistré
     *
     * @param array $data_element
     * @return int
     */
    public function isExiste(array $data_element):int
    {
        $element =$this->repository->findElement($data_element);
        if ($element){ // si trouvé alors on fait modification
            parent::update($element, $data_element);
        }else{ // on insert
            $data = parent::create($data_element);
            $element= $data['element'];
        }
        return $element->id;
    }


    public function deleteFamille($model)
    {
        try {
            if ($model->articles()->exists()) {
                return $this->errorResponse('Impossible de supprimer cet élément car il est associé à des articles.', new Exception('Contrainte d\'intégrité référentielle'));
            }
            return parent::delete($model);
        }catch (Exception $e){
            return $this->errorResponse('Erreur lors de la suppression', $e);
        }
    }
}
