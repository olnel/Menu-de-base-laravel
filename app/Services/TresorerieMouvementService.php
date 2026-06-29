<?php

namespace App\Services;

use App\constants\Messagenotification;
use App\Models\TresorerieMouvement;
use App\Repositories\TresorerieMouvementRepository;
use App\Repositories\TresorerieRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class TresorerieMouvementService extends BaseService
{
    protected $repository;
    protected TresorerieFluxService $fluxService;
    protected TresorerieRepository $tresorerierepository;

    protected array $scope = [
        'filter' => 'search',
        'tresorerie' => 'tresorerie_id',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date'
    ];

    public function __construct(
                                TresorerieMouvementRepository $tresorerieMouvementRepository,
                                TresorerieFluxService $tresorerieFluxService,
                                TresorerieRepository $tresorerieRepository,
                                private readonly PosteDepenseService $posteDepenseService)
    {
        $this->repository = $tresorerieMouvementRepository;
        $this->fluxService = $tresorerieFluxService;
        $this->tresorerierepository = $tresorerieRepository;
        parent::__construct($tresorerieMouvementRepository);
    }

    private function posteDepense($validated)
    {
        if ($validated['type_mvt'] === 'SORTIE' && empty($validated['poste_depense'])) {
            throw new Exception('Le champ poste_depense est requis pour un mouvement de type Décaissement');
        }
        $this->posteDepenseService->createOrEdit($validated['poste_depense']);
    }
    /**
     * Crée un nouveau mouvement de trésorerie
     */
    public function createMouvement(array $data): array
    {

        try {
            $data['user_id'] = auth()->id();
            // Validation supplémentaire si nécessaire
            $this->validateMouvementData($data);

            // Traitement des flux
            $this->fluxService->processMouvement($data);

            // Création du mouvement principal
            $this->posteDepense($data);
            return parent::create($data);

        } catch (Exception $e) {

            Log::error(MessageNotification::MSG_ERROR_INSERT . $e->getMessage());
            return $this->errorResponse(MessageNotification::MSG_ERROR_INSERT, $e);
        }
    }

    /**
     * Met à jour un mouvement existant
     */
    public function updateMouvement(TresorerieMouvement $mouvement, array $data): array
    {

        try {
            $data['user_id'] = auth()->id();

            // Annulation des flux existants
            $this->fluxService->cancelMouvement($mouvement->toArray());

            // Traitement des nouveaux flux
            $this->fluxService->processMouvement($data);

            // Mise à jour du mouvement
            $this->posteDepense($data);
            return parent::update($mouvement, $data);

        } catch (Exception $e) {

            Log::error(MessageNotification::MSG_ERROR_UPDATE . $e->getMessage());
            return $this->errorResponse(MessageNotification::MSG_ERROR_UPDATE, $e);
        }
    }

    /**
     * Supprime un mouvement
     */
    public function deleteMouvement(TresorerieMouvement $mouvement): array
    {

        try {
            // Annulation des flux
            $this->fluxService->cancelMouvement($mouvement->toArray());
            // Suppression du mouvement
            return parent::delete($mouvement);
        } catch (Exception $e) {

            Log::error(MessageNotification::MSG_ERROR_DELETE . $e->getMessage());
            return $this->errorResponse(MessageNotification::MSG_ERROR_DELETE, $e);
        }
    }

    /**
     * Validation des données de mouvement
     */
    private function validateMouvementData(array $data): void
    {
        // Validation supplémentaire si nécessaire
        if ($data['type_mvt'] === 'TRANSFERT' && empty($data['tresorerie_id_cible'])) {
            throw new Exception('La trésorerie cible est requise pour un transfert');
        }
        if ($data['montant'] <= 0) {
            throw new Exception('Le montant doit être positif');
        }
    }
}
