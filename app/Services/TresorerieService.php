<?php

namespace App\Services;

use App\constants\Messagenotification;
use App\Models\Basemodel;
use App\Repositories\TresorerieRepository;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;

class TresorerieService extends BaseService
{
    protected $repository;

    public function __construct(TresorerieRepository $tresorerieRepository)
    {
        $this->repository = $tresorerieRepository;
        parent::__construct($tresorerieRepository);
    }

    public function updateTresoreie(\App\Models\Tresorerie $tresorerie, mixed $validated)
    {
        try {
            if ($validated['type_tresorerie'] === 'Mobile Money' && (empty($validated['numero_telephone']))) {
                return $this->errorResponse(
                    'Le numéro de téléphone est requis pour le type Mobile Money',
                    new Exception('Erreur de validation')
                );
            }

            parent::update($tresorerie,$validated);
            return $this->successResponse(Messagenotification::MSG_UPDATE_SUCCESS);
        } catch (Exception $e) {
            return $this->errorResponse(Messagenotification::MSG_ERROR_UPDATE, $e);
        }
    }


    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('nom_tresorerie');
    }

    public function saveTresorerie (array $validated)
    {
        try {
            if ($validated['type_tresorerie'] === 'Mobile Money' && (empty($validated['numero_telephone']))) {
                return $this->errorResponse(
                    'Le numéro de téléphone est requis pour le type Mobile Money',
                    new Exception('Erreur de validation')
                );
            }

            parent::create($validated);
            return $this->successResponse(Messagenotification::MSG_INSERT_SUCCESS);
        } catch (Exception $e) {

            return $this->errorResponse(Messagenotification::MSG_ERROR_UPDATE, $e);
        }

    }
}
