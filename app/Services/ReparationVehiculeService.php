<?php

namespace App\Services;

use App\Models\ReparationVehicule;
use App\Models\VehiculeElement;
use App\Models\Remorque;
use App\Models\RemorqueElement;
use App\Models\Ticket;
use App\Repositories\ReparationVehiculeRepository;
use App\Services\Base\BaseService;
use App\Services\ArticleMouvementService;
use App\Services\TicketService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class ReparationVehiculeService extends BaseService
{

    public function __construct(
        ReparationVehiculeRepository $reparationVehiculeRepository,
        protected ArticleMouvementService $articleMouvementService,
        protected TicketService $ticketService
    ) {
        parent::__construct($reparationVehiculeRepository);
    }

    public function save($requestData, ReparationVehicule $reparation)
    {
        try {
            DB::beginTransaction();
            $dateHeure = now();

            // 1. Revert previous stock movements if this is an update
            if ($reparation->exists) {
                $this->revertStockImpact($reparation, $dateHeure);
            }

            $reparation->fill($requestData);
            $reparation->user_id = Auth::id();

            // Default responsable_id to current user if not provided
            // if (!$reparation->responsable_id) {
            //     $reparation->responsable_id = Auth::id();
            // }

            // Recalculate global totals
            $globalPrixPieces = 0;
            $globalPrixMO = 0;

            // First save to get an ID if new
            $reparation->save();

            // Handle Articles and Details
            $articleIds = [];
            foreach ($requestData['articles'] as $articleData) {
                $artPrixPieces = 0;
                $artPrixMO = 0;

                $article = $reparation->articles()->updateOrCreate(
                    ['id' => $articleData['id'] ?? null],
                    $articleData
                );
                $articleIds[] = $article->id;

                $detailIds = [];
                foreach ($articleData['details'] as $detailData) {
                    // Line calculations
                    $detailData['montant_pieces_article'] = ($detailData['quantite_article'] ?? 0) * ($detailData['prix_unitaire_article'] ?? 0);

                    // Labor cost calculation (Rate * Hours or Fixed)
                    if (($detailData['nbre_heure'] ?? 0) > 0 && ($detailData['tarifs_horaires'] ?? 0) > 0) {
                        $detailData['total_main_oeuvre'] = $detailData['nbre_heure'] * $detailData['tarifs_horaires'];
                    }

                    if (($detailData['nbre_heure_changer'] ?? 0) > 0 && ($detailData['tarifs_horaires_changer'] ?? 0) > 0) {
                        $detailData['total_main_oeuvre_changer'] = $detailData['nbre_heure_changer'] * $detailData['tarifs_horaires_changer'];
                    }

                    $detailData['quantite_article_avant'] = $detailData['quantite_article'] ?? 0;
                    $detail = $article->details()->updateOrCreate(
                        ['id' => $detailData['id'] ?? null],
                        $detailData
                    );
                    $detailIds[] = $detail->id;

                    // Aggregate article totals
                    $artPrixPieces += $detail->montant_pieces_article;
                    $artPrixMO += ($detail->total_main_oeuvre ?? 0) + ($detail->total_main_oeuvre_changer ?? 0);

                    // 2. Apply new stock movement (Sortie)
                    if ($detail->article_id && $detail->magasin_id && $detail->quantite_article > 0) {
                        $mouvement = [
                            'date_mvt' => Carbon::parse($reparation->date_reparation)->format('Y-m-d'),
                            'article_id' => $detail->article_id,
                            'magasin_id' => $detail->magasin_id,
                            'user_id' => Auth::id(),
                            'date_heure_enregistrement' => $dateHeure,
                            'operation_mvt' => 'SORTIE MAINTENANCE',
//                            'commentaire_mvt' => 'Maintenance Véhicule: ' . $reparation->immatriculation,
                            'commentaire_mvt' => 'Maintenance Véhicule: ' . $reparation->immatriculation .
                                ($article->is_remorque
                                    ? '/' . $article->numero_remorque
                                    : '') .
                                ($article->is_consommable ? '/consommable' : ''),
                            'reference_mvt' => 'MAINT-' . $reparation->id,
                            'qte_mvt' => $detail->quantite_article,
                        ];
                        $this->articleMouvementService->sortieMouvement($mouvement);
                    }

                    // 3. Generate ticket for this detail immediately (works for both store and update)
                    $this->ticketService->generateForDetail($detail);

                    // 4. Synchronize with VehiculeElement or RemorqueElement
                    if ($detail->reference_article || $detail->designation_article) {
                        if ($article->is_remorque) {
                            $remorque = Remorque::where('numero_remorque', $article->numero_remorque)->first();
                            if ($remorque) {
                                RemorqueElement::updateOrCreate(
                                    [
                                        'remorque_id' => $remorque->id,
                                        'reference' => $detail->reference_article,
                                        'libelle' => $detail->libelle ?: $detail->designation_article,
                                    ],
                                    [
                                        'emplacement' => $detail->emplacement_article,
                                        'numero_serie' => $detail->numero_serie_article,
                                        'etat_piece' => 'Neuf',
                                    ]
                                );
                                //Affectation dans le nouveau table
                                $remorque->pieces()->updateOrCreate(
                                    [
                                        'reference' => $detail->reference_article,
                                        'libelle' => $detail->libelle ?: $detail->designation_article,
                                    ],
                                    [
                                        'emplacement' => $detail->emplacement_article,
                                        'numero_serie' => $detail->numero_serie_article,
                                    ]
                                );
                            }
                        } else {
                            VehiculeElement::updateOrCreate(
                                [
                                    'vehicule_id' => $reparation->vehicule_id,
                                    'reference' => $detail->reference_article,
                                    'libelle' => $detail->libelle ?: $detail->designation_article,
                                ],
                                [
                                    'emplacement' => $detail->emplacement_article,
                                    'numero_serie' => $detail->numero_serie_article,
                                    'etat_piece' => 'Neuf',
                                ]
                            );
                            //Affectation dans le nouveau table
                            $reparation->vehicule->pieces()->updateOrCreate(
                                [
                                    'reference' => $detail->reference_article,
                                    'libelle' => $detail->libelle ?: $detail->designation_article,
                                ],
                                [
                                    'emplacement' => $detail->emplacement_article,
                                    'numero_serie' => $detail->numero_serie_article,
                                ]
                            );
                        }
                    }
                }

                // Clean up tickets for deleted details
                $deletedDetails = $article->details()->whereNotIn('id', $detailIds)->get();
                foreach ($deletedDetails as $deletedDetail) {
                    Ticket::where('reparation_vehicule_article_detail_id', $deletedDetail->id)->delete();
                }
                $article->details()->whereNotIn('id', $detailIds)->delete();

                // Update article totals
                $article->update([
                    'prix_total_pieces' => $artPrixPieces,
                    'prix_total_main_oeuvre' => $artPrixMO,
                    'montant_total' => $artPrixPieces + $artPrixMO
                ]);

                $globalPrixPieces += $artPrixPieces;
                $globalPrixMO += $artPrixMO;
            }
            // Clean up tickets for deleted articles (their details' tickets too)
            $deletedArticles = $reparation->articles()->whereNotIn('id', $articleIds)->get();
            foreach ($deletedArticles as $deletedArticle) {
                $deletedArticle->details->each(function ($detail) {
                    Ticket::where('reparation_vehicule_article_detail_id', $detail->id)->delete();
                });
            }
            $reparation->articles()->whereNotIn('id', $articleIds)->delete();

            // Final update for global totals
            $reparation->update([
                'prix_total_pieces' => $globalPrixPieces,
                'prix_total_main_oeuvre' => $globalPrixMO,
                'montant_total' => $globalPrixPieces + $globalPrixMO
            ]);

            DB::commit();
            return $this->successResponse('Maintenance enregistrée avec succès', $reparation);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de l\'enregistrement', $e);
        }
    }

    public function revertStockImpact(ReparationVehicule $reparation, $dateHeure = null)
    {
        if (!$dateHeure) $dateHeure = now();
        $reparation->load('articles.details');
        foreach ($reparation->articles as $article) {
            foreach ($article->details as $detail) {
                if ($detail->article_id && $detail->magasin_id && $detail->quantite_article_avant > 0) {
                    $mouvement = [
                        'date_mvt' => now()->format('Y-m-d'),
                        'article_id' => $detail->article_id,
                        'magasin_id' => $detail->magasin_id,
                        'user_id' => Auth::id(),
                        'date_heure_enregistrement' => $dateHeure,
                        'operation_mvt' => 'ENTREE MAINTENANCE',
//                        'commentaire_mvt' => 'Correction/Update Maintenance: ' . $reparation->immatriculation,
                        'commentaire_mvt' => 'Correction/Update Maintenance: ' . $reparation->immatriculation .
                            ($article->is_remorque
                                ? '/' . $article->numero_remorque
                                : '') .
                            ($article->is_consommable ? '/consommable' : ''),
                        'reference_mvt' => 'MAINT-' . $reparation->id,
                        'qte_mvt' => $detail->quantite_article_avant,
                    ];
                    $this->articleMouvementService->entrerMouvement($mouvement);
                }
            }
        }
    }

    public function deleteReparation(ReparationVehicule $reparation)
    {
        try {
            DB::beginTransaction();

            // Revert stock impact before deleting
            $this->revertStockImpact($reparation);

            $reparation->delete();

            DB::commit();
            return $this->successResponse('Réparation supprimée avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la suppression', $e);
        }
    }
}
