<?php

namespace App\Services;

use App\Repositories\CarburantTransactionRepository;
use App\Services\Base\BaseService;
use App\Models\CarburantCard;
use App\Models\CarburantTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class CarburantTransactionService extends BaseService
{
    protected $repository;
    private $carburantCardService;
    private $carburantMouvementService;
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

    public function __construct(CarburantTransactionRepository $categorieRepository, CarburantCardService $carburantCardService, CarburantMouvementService $carburantMouvementService)
    {
        $this->repository = $categorieRepository;
        $this->carburantCardService = $carburantCardService;
        $this->carburantMouvementService = $carburantMouvementService;
        parent::__construct($categorieRepository);
    }
    public function getAll(array $filters = [])
    {
        return parent::getAll($filters);
    }

    //Crée une nouvelle transaction de carburant.
    public function create(array $validated): array
    {
        $validated['user_id'] = Auth::id();
        $validated['date_heure_enregistrement'] = Carbon::now();
        // Gérer les pièces jointes
        if (isset($validated['piece_jointe'])) {
            $validated['piece_jointe'] = json_encode($this->generatePJ($validated['piece_jointe'], $validated['reference']));
        }
        $card = null;
        if (isset($validated['carburant_card_id']) && $validated['type'] === 'achat_carte') {
            $card = CarburantCard::find($validated['carburant_card_id']);
            if (!$card) {
                return ['error' => true, 'message' => 'Carte de carburant introuvable.'];
            }
            // Traiter l'achat via le CarburantCardService
            $purchaseResult = $this->carburantCardService->processCardPurchase($card, $validated['montant'], $validated);
            if (!empty($purchaseResult['error'])) {
                return $purchaseResult; // Retourne l'erreur si le solde est insuffisant
            }
        } else {
            // Appliquer le nouveau mouvement pour les achats en espèces
            $this->carburantMouvementService->createMouvement(
                null, // Pas de carte associée
                null, // Solde initial non applicable
                $validated['montant'],
                $validated['montant'], // Solde final non applicable
                'achat_espece',
                $validated['commentaire'] ?? null,
                Auth::id(),
                false,
                [
                    'date_mvmt' => $validated['date_transaction'] ?? Carbon::now(),
                    'reference_mvmt' => $validated['reference'] ?? '',
                    'chauffeur_id' => $validated['chauffeur_id'] ?? null,
                    'vehicule_id' => $validated['vehicule_id'] ?? null,
                ]
            );
        }
        // Création de la transaction (le mouvement a déjà été géré par CarburantCardService)
        $resultat = parent::create($validated);
        return $resultat;
    }

    //Met à jour une transaction existante.
    public function update($Id, array $validated): array
    {
        // Gérer les pièces jointes
        $existingFiles = !empty($validated['existing_files'])
            ? json_decode($validated['existing_files'], true)
            : [];
        $newFiles = isset($validated['piece_jointe'])
            ? $this->generatePJ($validated['piece_jointe'], $validated['type'])
            : [];
        $validated['piece_jointe'] = json_encode(array_merge($existingFiles, $newFiles));
        //annulation du mouvement et creation d'un nouveau
        $model = CarburantTransaction::find($Id);
        if (!$model) {
            return ['error' => true, 'message' => 'Transaction introuvable.'];
        }
        // Annuler l'ancien mouvement via CarburantCardService
        if ($model->carburant_card_id) {
            $cardBeforeUpdate = CarburantCard::find($model->carburant_card_id);
            if ($cardBeforeUpdate) {
                $this->carburantCardService->processCancellation($cardBeforeUpdate, $model->toArray());
            }
        } else {
            // Annuler l'ancien mouvement pour les achats en espèces
            $this->carburantMouvementService->createMouvement(
                null, // Pas de carte associée
                null, // Solde initial
                $model->montant,
                null, // Solde final
                'annulation_transaction',
                'annulation de la transaction espece ' . ($model->reference ?? ''),
                Auth::id(),
                false,
                [
                    'date_mvmt' => $model->date_transaction ?? Carbon::now(),
                    'reference_mvmt' => $model->reference ?? '',
                    'chauffeur_id' => $model->chauffeur_id ?? null,
                    'vehicule_id' => $model->vehicule_id ?? null,
                ]
            );
        }
        // Appliquer le nouveau mouvement après l'annulation de l'ancien
        if (isset($validated['carburant_card_id'])) {
            $cardAfterUpdate = CarburantCard::where('id', $validated['carburant_card_id'])->first();
            if (!$cardAfterUpdate) {
                return ['error' => true, 'message' => 'Nouvelle carte de carburant introuvable.'];
            }
            $purchaseResult = $this->carburantCardService->processCardPurchase($cardAfterUpdate, $validated['montant'], $validated);
            if (!empty($purchaseResult['error'])) {
                return $purchaseResult;
            }
        } else {
            // Appliquer le nouveau mouvement après l'annulation de l'ancien pour les achats en espèces
            $this->carburantMouvementService->createMouvement(
                null, // Pas de carte associée
                null, // Solde initial non applicable
                $validated['montant'],
                null, // Solde final non applicable
                'achat_espece',
                $validated['commentaire'] ?? null,
                Auth::id(),
                false,
                [
                    'date_mvmt' => $validated['date_transaction'] ?? Carbon::now(),
                    'reference_mvmt' => $validated['reference'] ?? '',
                    'chauffeur_id' => $validated['chauffeur_id'] ?? null,
                    'vehicule_id' => $validated['vehicule_id'] ?? null,
                ]
            );
        }
        return parent::update($model, $validated);
    }

    //Supprime une transaction.
    public function delete($model): array
    {
        if (!$model) {
            return ['error' => true, 'message' => 'Transaction introuvable pour suppression.'];
        }
        // Annuler le mouvement associé via CarburantCardService
        if ($model->carburant_card_id) {
            $card = CarburantCard::find($model->carburant_card_id);
            if ($card) {
                $this->carburantCardService->processCancellation($card, $model->toArray());
            }
        }
        // Annuler le mouvement pour les achats en espèces lors de la suppression
        else {
            $this->carburantMouvementService->createMouvement(
                null,
                0,
                -$model->montant,
                0,
                'annulation_transaction',
                'annulation de la transaction espece' . ($model->reference ?? ''),
                Auth::id(),
                false,
                [
                    'date_mvmt' => $model->date_transaction,
                    'reference_mvmt' => $model->reference,
                    'chauffeur_id' => $model->chauffeur_id ?? null,
                    'vehicule_id' => $model->vehicule_id ?? null,
                ]
            );
        }
        return parent::delete($model);
    }

    /**
     * Génère les chemins des pièces jointes téléchargées.
     *
     */
    private function generatePJ(array $pieceJoint, string $t): array
    {
        $uploadedPaths = [];
        foreach ($pieceJoint as $file) {
            if ($file instanceof UploadedFile) {
                $extension = $file->getClientOriginalExtension();
                $random = random_int(100000, 999999);
                $filename = $t . $random . '.' . $extension;
                $relativePath = "img/transaction/documents";
                $destinationPath = public_path($relativePath);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $uploadedPaths[] = "{$relativePath}/{$filename}";
            }
        }
        return $uploadedPaths;
    }
    public function getTotal($filtre)
    {
        return parent::countElement();
    }

    public function getTransactionType($filtre): Collection
    {
        $rows = $this->repository->getTransactionParType($filtre, $this->scope);
        // Map type
        $labelMap = ['achat_carte' => 'Achat par Carte', 'achat_espece' => 'Achat par Espèce',];
        return $rows->map(function ($row) use ($labelMap) {
            $label = $labelMap[$row->type] ?? $row->type;
            return [
                'label' => $label,
                'value' => $row->count,
                'total' => $row->total,
            ];
        });
    }

    //Retourné les vehicules par carburant consommé et transaction
    public function getTopVehiculeForCarburant(array $filtre)
    {
        $rows = $this->repository->getVehiculeByLitresTransaction($filtre, $this->scope);
        return $rows->map(function ($row) {
            return [
                'label' => $row->label,
                'value' => (float) $row->total_litres,
                'value2' => (int) ($row->total_tx ?? 0),
            ];
        });
    }

    //Retourne les cartes avec le plus de transactions
    public function getTopCardsByTransactions(array $filtre): Collection
    {
        $rows = $this->repository->getTopCardsByTransactions($filtre, $this->scope);
        return $rows->map(function ($row) {
            return [
                'label' => $row->label,
                'value' => (int) ($row->total_tx ?? 0),
            ];
        });
    }
}
