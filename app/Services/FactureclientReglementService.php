<?php

namespace App\Services;

use App\constants\Messagenotification;
use App\Models\FactureClient;
use App\Models\FactureClientReglement;
use App\Repositories\FactureclientReglementRepository;
use App\Repositories\FactureClientRepository;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class FactureclientReglementService extends BaseService
{

    protected $repository;

    public function __construct(
        FactureclientReglementRepository $factureclientReglementRepository,
        protected readonly TresorerieFluxService $tresorerieFluxService,
        protected readonly FactureClientRepository $factureClientRepository
    ) {
        $this->repository = $factureclientReglementRepository;
        parent::__construct($factureclientReglementRepository);
    }

    /**
     * Traite l'ajout d'un nouveau règlement de facture client.
     *
     * @param array $validated Les données validées du règlement.
     * @return mixed Le résultat de l'opération de création ou une réponse d'erreur.
     */
    public function todoReglement(array $validated)
    {
        try {
            $validated['user_id'] = auth()->id();
            $facture = $this->factureClientRepository->findElement(['id' => $validated['facture_client_id']]);

            if (!$facture) {
                throw new Exception("Facture client introuvable pour l'ID: " . $validated['facture_client_id']);
            }

            // Prépare les données pour insérer dans la trésorerie flux
            $dataFlux = $this->prepareDataTresorerieFlux($facture, $validated);

            // Écriture dans la table trésorerie flux
            $this->tresorerieFluxService->processMouvement($dataFlux);

            // Modification du statut de la facture client
            $dataFacture = $this->calculateFactureUpdateData($facture, $validated['montant_reglement']);

            // Mise à jour de la facture client
            $this->factureClientRepository->update($facture, $dataFacture);

            $reglement = parent::create($validated);


            return $reglement;

        } catch (Exception $e) {

            Log::error(MessageNotification::MSG_ERROR_INSERT . $e->getMessage());
            return $this->errorResponse(MessageNotification::MSG_ERROR_INSERT, $e);
        }
    }

    /**
     * Calcule les nouvelles données de la facture après un règlement.
     *
     * @param FactureClient $facture La facture client actuelle.
     * @param float $montantReglement Le montant du règlement.
     * @return array Les données mises à jour pour la facture.
     */
    protected function calculateFactureUpdateData(FactureClient $facture, float $montantReglement): array
    {
        $newMontantPayer = $facture->montant_payer + $montantReglement;
        $newMontantResteAPayer = $facture->montant_reste_a_payer - $montantReglement;

        $statutFacture = match (true) {
            $newMontantResteAPayer <= 0 => "Payée",
            $newMontantResteAPayer > 0 => 'Partiellement payée',
            default => $facture->statut_facture,
        };

        return [
            'montant_payer' => $newMontantPayer,
            'montant_reste_a_payer' => $newMontantResteAPayer,
            'statut_facture' => $statutFacture,
        ];
    }


    /**
     * Prépare les données pour un mouvement de trésorerie.
     *
     * @param FactureClient $facture La facture client concernée.
     * @param array $validated Les données validées du règlement.
     * @param string $typeMvt Le type de mouvement (ENTREE/SORTIE).
     * @param string $operationMvt L'opération de mouvement.
     * @return array Les données formatées pour la trésorerie.
     */
    public function prepareDataTresorerieFlux(FactureClient $facture, array $validated, string $typeMvt = 'ENTREE', string $operationMvt = 'REGLEMENT FACTURE'): array
    {
        return [
            'date_mvt' => $validated['date_reglement'],
            'montant' => $validated['montant_reglement'],
            'mode_paiement' => $validated['mode_reglement'],
            'commentaire' => $validated['commentaire'] ?? null,
            'tresorerie_id' => $validated['tresorerie_id'],
            'type_mvt' => $typeMvt,
            'user_id' => auth()->id(),
            'libelle_mvt' => 'Réglement de la facture client: ' . $facture->nom_client . ' - ' . $facture->numero_facture,
            'operation_mvt' => $operationMvt,
        ];
    }

    /**
     * Affiche l'historique des règlements pour une facture client donnée.
     *
     * @param FactureClient $factureclient La facture client.
     * @return \Illuminate\Database\Eloquent\Collection Les règlements historiques.
     */
    public function showHistoriqueReglement(FactureClient $factureclient)
    {
        return $this->repository->get(['facture_client_id' => $factureclient->id]);
    }

    /**
     * Supprime un règlement et annule les mouvements associés.
     *
     * @param FactureClientReglement $factureclientreglement Le règlement à supprimer.
     * @return mixed Le résultat de l'opération de suppression ou une réponse d'erreur.
     */
    public function deleteReglement(FactureClientReglement $factureclientreglement)
    {


        try {
            $facture = $this->factureClientRepository->findElement(['id' => $factureclientreglement->facture_client_id]);

            if (!$facture) {
                throw new Exception("Facture client associée au règlement introuvable.");
            }

            // Prépare les données pour le mouvement de trésorerie inverse
            $dataFluxInverse = $this->prepareDataTresorerieFlux(
                $facture,
                [
                    'date_reglement' => now()->format('Y-m-d'),
                    'montant_reglement' => $factureclientreglement->montant_reglement,
                    'mode_reglement' => $factureclientreglement->mode_reglement,
                    'commentaire' => 'Annulation règlement client: ' . $factureclientreglement->id,
                    'tresorerie_id' => $factureclientreglement->tresorerie_id,
                ],
                'SORTIE',
                'ANNULATION REGLEMENT FACTURE'
            );
            $this->tresorerieFluxService->processMouvement($dataFluxInverse);

            // Calcule les nouvelles données de la facture après annulation
            $newMontantPayer = $facture->montant_payer - $factureclientreglement->montant_reglement;
            $newMontantResteAPayer = $facture->montant_reste_a_payer + $factureclientreglement->montant_reglement;

            $newStatutFacture = match (true) {
                $newMontantPayer <= 0 => 'Envoyée',
                $newMontantResteAPayer > 0 => 'Partiellement payée',
                default => 'Payée',
            };

            $dataFactureUpdate = [
                'montant_payer' => $newMontantPayer,
                'montant_reste_a_payer' => $newMontantResteAPayer,
                'statut_facture' => $newStatutFacture,
            ];
            $this->factureClientRepository->update($facture, $dataFactureUpdate);
            $result = parent::delete($factureclientreglement);

            return $result;

        } catch (Exception $e) {
            Log::error(MessageNotification::MSG_ERROR_DELETE . $e->getMessage());
            return $this->errorResponse(MessageNotification::MSG_ERROR_DELETE, $e);
        }
    }
}
