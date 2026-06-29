<?php

namespace App\Services;

use App\Models\Voyage;
use App\Repositories\VoyageChargeRepository;
use App\Services\Base\BaseService;
use App\Services\TresorerieFluxService;
use Illuminate\Support\Facades\Auth;
use App\Models\VoyageCharge;

class VoyageChargeService extends BaseService
{
    protected $repository;
    protected TresorerieFluxService $tresorerieFluxService;

    public function __construct(VoyageChargeRepository $repository, TresorerieFluxService $tresorerieFluxService)
    {
        $this->repository = $repository;
        $this->tresorerieFluxService = $tresorerieFluxService;
        parent::__construct($repository);
    }

    public function syncVoyageCharges(Voyage $voyage, array $newItems, int $tresorerie_id, string $mode_paiement): array
    {
        $existingCharges = $voyage->voyageCharges()->get()->keyBy('id');
        $updatedIds = [];

        foreach ($newItems as $itemData) {
            // Les tresorerie_id et mode_paiement pour toutes les charges
            $itemData['tresorerie_id'] = $tresorerie_id;
            $itemData['mode_paiement'] = $mode_paiement;
            $isNew = !isset($itemData['id']) || empty($itemData['id']);

            if ($isNew) {
                // Crée nouveau charge
                $charge = $voyage->voyageCharges()->create($itemData);
                $updatedIds[] = $charge->id;
                // Enregistrer flux de trésorerie
                $this->tresorerieFluxService->processMouvement([
                    'date_mvt' => now(),
                    'libelle_mvt' => 'Charge Voyage: ' . $itemData['libelle'],
                    'tresorerie_id' => $itemData['tresorerie_id'],
                    'type_mvt' => 'SORTIE',
                    'montant' => $itemData['montant'],
                    'operation_mvt' => 'Paiement Charge Voyage',
                    'mode_paiement' => $itemData['mode_paiement'],
                    'commentaire' => 'Charge liée au voyage N°' . $voyage->numero_voyage,
                    'user_id' => Auth::id(),
                ]);
            } else {
                // Mise à jour d'une charge existante
                $charge = $existingCharges->pull($itemData['id']);
                if ($charge) {
                    // Si montant ou tresorerie_id ou mode_paiement change annuler et creer un autre
                    if ($charge->montant != $itemData['montant'] || $charge->tresorerie_id != $itemData['tresorerie_id'] || $charge->mode_paiement != $itemData['mode_paiement']) {
                        // Annuler le mouvement précédent d'abord
                        $this->tresorerieFluxService->cancelMouvement([
                            'tresorerie_id' => $charge->tresorerie_id,
                            'montant' => $charge->montant,
                            'type_mvt' => 'SORTIE',
                            'libelle_mvt' => 'Annulation Charge Voyage: ' . $charge->libelle,
                            'commentaire' => 'Annulation suite à modification de la charge Voyage N°' . $voyage->numero_voyage,
                            'user_id' => Auth::id(),
                        ]);
                        // Créer nouveau flux
                        $this->tresorerieFluxService->processMouvement([
                            'date_mvt' => now(),
                            'libelle_mvt' => 'Charge Voyage: ' . $itemData['libelle'],
                            'tresorerie_id' => $itemData['tresorerie_id'],
                            'type_mvt' => 'SORTIE',
                            'montant' => $itemData['montant'],
                            'operation_mvt' => 'Paiement Charge Voyage',
                            'mode_paiement' => $itemData['mode_paiement'],
                            'commentaire' => 'Charge liée au voyage N°' . $voyage->numero_voyage,
                            'user_id' => Auth::id(),
                        ]);
                    }
                    $charge->update($itemData);
                    $updatedIds[] = $charge->id;
                }
            }
        }
        // Supprimer les charges qui ne sont plus dans newItems
        foreach ($existingCharges as $chargeToDelete) {
            // Annuler le mouvement de trésorerie associé
            $this->tresorerieFluxService->cancelMouvement([
                'tresorerie_id' => $chargeToDelete->tresorerie_id,
                'montant' => $chargeToDelete->montant,
                'type_mvt' => 'SORTIE',
                'libelle_mvt' => 'Suppression Charge Voyage: ' . $chargeToDelete->libelle,
                'commentaire' => 'Annulation de la charge Voyage N°' . $voyage->numero_voyage,
                'user_id' => Auth::id(),
            ]);
            $chargeToDelete->delete();
        }
        return ['error' => false, 'message' => 'Charges mis à jour avec succès.'];
    }

    public function deleteCharge(VoyageCharge $charge): array
    {
        try {
            // Annuler le mouvement trezesorerie
            $this->tresorerieFluxService->cancelMouvement([
                'tresorerie_id' => $charge->tresorerie_id,
                'montant' => $charge->montant,
                'type_mvt' => 'SORTIE',
                'libelle_mvt' => 'Suppression Charge Voyage: ' . $charge->libelle,
                'commentaire' => 'Suppression de la charge Voyage N°' . $charge->voyage->numero_voyage,
                'user_id' => Auth::id(),
            ]);
            // Supprimer la charghe
            $charge->delete();
            return ['error' => false, 'message' => 'Charge supprimée avec succès.'];
        } catch (\Exception $e) {
            return ['error' => true, 'message' => 'Erreur lors de la suppression de la charge'];
        }
    }
}
