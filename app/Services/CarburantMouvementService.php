<?php

namespace App\Services;
use App\Repositories\CarburantMouvementRepository;
use App\Services\Base\BaseService;
use App\Models\CarburantCard;
use App\Models\CarburantMouvement;
use App\Models\CardAjustement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CarburantMouvementService extends BaseService
{
    protected $repository;
    protected array $scope = [
        'filter' => 'search',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date',
        'filterchauffeur' => 'chauffeur_id',
        'filtervehicule' => 'vehicule_id',
        'filteruser' => 'user_id',
        'filtertypemvmt' => 'type_mvmt',
        'filtercarburantcard' => 'carburant_card_id'
    ];
    public function __construct(CarburantMouvementRepository $carbcardRepository)
    {
        $this->repository = $carbcardRepository;
        parent::__construct($carbcardRepository);
    }
    public function getAll(array $filters = [])
    {
        return parent::getAll($filters);
    }


    public function createMouvement(
        ?CarburantCard $card = null,
        ?float $initialAmount = null,
        ?float $movementAmount = null,
        ?float $finalAmount = null,
        string $type,
        ?string $motif = null,
        ?int $userId = null,
        bool $isManualAdjustment = false,
        array $extraData = []
    ): CarburantMouvement {
        $data = [
            'date_mvmt' => $extraData['date_mvmt'] ?? Carbon::now(),
            'date_heure_enregistrement' => Carbon::now(),
            'type' => $type,
            'montant' => $movementAmount,
            'carburant_card_id' => $card->id ?? null,
            'chauffeur_id' => $extraData['chauffeur_id'] ?? null,
            'vehicule_id' => $extraData['vehicule_id'] ?? null,
            'user_id' => $userId ?? Auth::id(),
            'commentaire' => $motif,
            'montant_initiale' => $initialAmount,
            'montant_mvmt' => $movementAmount,
            'montant_finale' => $finalAmount,
            'reference_mvmt' => $extraData['reference_mvmt'] ?? null,
        ];
        $mouvement = CarburantMouvement::create($data);
        //si c'est un ajustement crée
        if ($isManualAdjustment) {
            CardAjustement::create([
                'carburant_mouvement_id' => $mouvement->id,
                'date' => Carbon::now(),
                'user_id' => $userId ?? Auth::id(),
                'montant' => $movementAmount,
                'motif' => $motif,
            ]);
        }
        return $mouvement;
    }

    /**
     * Récupère les statistiques des mouvements carburant par type
     */
    public function getMouvementByType(array $filters = [])
    {
        $rows = $this->repository->getMouvementParType($filters, $this->scope);
        $data = [];
        $annee = date('Y');
        foreach ($rows as $row) {
            $typeLabel = $this->getTypeLabel($row->type);
            $data[$typeLabel] = (int) $row->count;
        }
        return [
            'data' => $data,
            'annee' => $annee
        ];
    }

    /**
     * Récupère le label lisible du type de mouvement
     */
    private function getTypeLabel($type)
    {
        return match ($type) {
            'achat_carte' => 'Achat par Carte',
            'achat_espece' => 'Achat par Espèce',
            'recharge' => 'Recharge',
            'ajustement' => 'Ajustement',
            'annulation_transaction' => 'Annulation Transaction',
            default => $type,
        };
    }
}
