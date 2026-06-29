<?php

namespace App\Services;

use App\Repositories\CarburantCardRepository;
use App\Services\Base\BaseService;
use App\Models\CarburantCard;
use App\Services\CarburantMouvementService;
use Illuminate\Support\Facades\Auth;

class CarburantCardService extends BaseService
{
    protected $repository;
    protected $carburantMouvementService;
    protected array $scope = ['filter' => 'search', 'filteractive' => 'active',];

    public function __construct(CarburantCardRepository $carbcardRepository, CarburantMouvementService $carburantMouvementService)
    {
        $this->repository = $carbcardRepository;
        $this->carburantMouvementService = $carburantMouvementService;
        parent::__construct($carbcardRepository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
            ->setFilterLabel('numero_carte');
    }

    public function getAll(array $filters = [])
    {
        return parent::getAll($filters);
    }

    public function updateStatus(CarburantCard $carburantCard): array
    {
        try {
            $carburantCard->update([
                'active' => !$carburantCard->active
            ]);
            return ['message' => 'Statut de la carte carburant mis à jour avec succès.'];
        } catch (\Exception $e) {
            return ['error' => true, 'message' => 'Erreur lors de la mise à jour du statut : ' . $e->getMessage()];
        }
    }

    //Gère la recharge(recharge mensuelle) ou l'ajustement manuel d'une carte de carburant.
    public function processRechargeOrAdjustment(CarburantCard $card, ?float $amount = null, ?string $motif = null, ?int $userId = null, bool $isManualAdjustment = false)
    {
        $qteInitial = $card->solde;
        $rechargeAmount = $amount ?? $card->plafond_mensuel;
        $type = $amount ? 'ajustement' : 'recharge';
        // Calcul du nouveau solde
        $newSolde = $rechargeAmount;
        // Test si nouveau solde ne dépasse pas le plafond
        if ($newSolde > $card->plafond_mensuel) {
            return ['error' => true, 'message' => 'Le solde final de la carte (' . $newSolde . ' Ar) dépasserait le plafond mensuel autorisé (' . $card->plafond_mensuel . ' Ar).'];
        }
        // 1. Mettre à jour le solde de la carte en premier
        $card->update(['solde' => $newSolde]);
        // 2. Enregistrer le mouvement de carburant
        $this->carburantMouvementService->createMouvement(
            $card,
            $qteInitial,
            $rechargeAmount,
            $newSolde,
            $type,
            $motif,
            $userId,
            $isManualAdjustment
        );
        return ['message' => 'Opération effectuée avec succès.'];
    }

    //Effectue une ou plusieurs recharges manuelles pour des cartes spécifiées.
    public function rechargeCarburantCards(array $cardIds, ?float $amount = null, ?string $motif = null, int $userId)
    {
        $errorMessages = [];
        $cardsToRecharge = CarburantCard::whereIn('id', $cardIds)->get();
        foreach ($cardsToRecharge as $card) {
            $output = $this->processRechargeOrAdjustment(
                $card,
                $amount,
                $motif,
                $userId,
                true // C'est toujours un ajustement manuel lors d'une recharge groupée
            );
            if (!empty($output['error'])) {
                $errorMessages[] = "Ajustement de la carte {$card->numero_carte} a échoué : " . $output['message'];
            }
        }
        if (!empty($errorMessages)) {
            return ['error' => true, 'message' => implode("\n", $errorMessages)];
        }
        return ['message' => 'Recharge effectuée avec succès.'];
    }

    //Traite un achat de carburant pour une carte.
    public function processCardPurchase(CarburantCard $card, float $amount, array $transactionData)
    {
        $initialSolde = $card->solde;
        if ($initialSolde < $amount) {
            return ['error' => true, 'message' => 'Solde insuffisant pour effectuer cet achat. Solde actuel : ' . $initialSolde . ' Ar.'];
        }
        $finalSolde = $initialSolde - $amount;
        // 1. Mettre à jour le solde de la carte
        $card->update(['solde' => $finalSolde]);
        // 2. Enregistrer le mouvement de carburant
        $this->carburantMouvementService->createMouvement(
            $card,
            $initialSolde,
            $amount,
            $finalSolde,
            'achat_carte',
            $transactionData['commentaire'] ?? null,
            Auth::id(),
            false, // Ce n'est pas un ajustement manuel
            [
                'date_mvmt' => $transactionData['date_transaction'],
                'reference_mvmt' => $transactionData['reference'],
                'chauffeur_id' => $transactionData['chauffeur_id'] ?? null,
                'vehicule_id' => $transactionData['vehicule_id'] ?? null,
            ]
        );
        return ['message' => 'Achat traité avec succès.'];
    }

    // Annule un mouvement lié à une carte et restaure son solde.
    public function processCancellation(?CarburantCard $card, array $transactionData)
    {
        $initialSolde = $card->solde ?? 0;
        $movementAmount = $transactionData['montant'];
        $finalSolde = $initialSolde + $movementAmount;
        // Si une carte est associée, mettre à jour son solde
        if ($card) { $card->update(['solde' => $finalSolde]);}
        // Enregistrer le mouvement d'annulation
        $this->carburantMouvementService->createMouvement(
            $card,
            $initialSolde,
            $movementAmount,
            $finalSolde,
            'annulation_transaction',
            'annulation de la transaction ' . ($transactionData['reference'] ?? ''),
            Auth::id(),
            false,
            [
                'date_mvmt' => $transactionData['date_transaction'],
                'reference_mvmt' => $transactionData['reference'],
                'chauffeur_id' => $transactionData['chauffeur_id'] ?? null,
                'vehicule_id' => $transactionData['vehicule_id'] ?? null,
            ]
        );
        return ['message' => 'Annulation traitée avec succès.'];
    }

    public function getMappedCarburantCards()
    {
        return $this->getAll([])->map(function ($card) {
            return [
                'value' => $card->id,
                'label' => $card->numero_carte,
                'solde' => $card->solde
            ];
        });
    }
    public function getTotal($filtre)
    {
        $countTotal = parent::countElement($filtre);
        $statusCard = $this->getCardStatus([]);
        return ['total' => $countTotal, 'statusCard' => $statusCard,];
    }
    public function getCardStatus($filtre)
    {
        $data = parent::getAll($filtre)->all();
        return array_reduce($data, function ($carry, $item) {
            if ($item->active === true) {
                $carry['cardActive']++;
            } else {
                $carry['cardDesactive']++;
            }
            return $carry;
        }, ['cardActive' => 0, 'cardDesactive' => 0,]);
    }

}
