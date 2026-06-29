<?php

namespace App\Services;

use App\Repositories\ArticleApprovisionnementRepository;
use App\Services\Base\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\PneuSerieService;
use App\Services\PneuMouvementService;
use Illuminate\Support\Facades\Auth;
use App\Models\ArticleApprovisionnement;
use App\Models\Article;



/**
 * Service de gestion des approvisionnements d'articles.
 */
class ArticleApprovisionnementService extends BaseService
{
    protected array $scope = ['filter' => 'search', 'magasin' => 'magasin_id', 'filterdatestart' => 'start_date', 'filterdateend' => 'end_date'];


    private ArticleApprovisionnementDetailService $articleApproDetailsService;
    private ArticleMouvementService $articleMouvementService;
    private FournisseurService $fournisseurService;
    private PneuSerieService $pneuSerieService;
    private PneuMouvementService $pneuMouvementService;

    public function __construct(
        ArticleApprovisionnementRepository $approvisionnementRepository,
        ArticleApprovisionnementDetailService $articleApprovisionnementDetailService,
        ArticleMouvementService $articleMouvementService,
        FournisseurService $fournisseurService,
        PneuSerieService $pneuSerieService,
        PneuMouvementService $pneuMouvementService
    ) {
        parent::__construct($approvisionnementRepository);

        $this->articleApproDetailsService = $articleApprovisionnementDetailService;
        $this->articleMouvementService = $articleMouvementService;
        $this->fournisseurService = $fournisseurService;
        $this->pneuSerieService = $pneuSerieService;
        $this->pneuMouvementService = $pneuMouvementService;
    }

    public function dashboard(array $filtre): array
    {
        return $this->repository->recapApprovisionnement($filtre, $this->scope);
    }

    /**
     * Supprime un approvisionnement avec ses détails et mouvements liés.
     */
    public function delete($model): array
    {
        $dateHeure = now();

        DB::beginTransaction();
        try {
            $data = $model->load(['details'])->toArray();

            // 1. Annulation des mouvements et mise à jour des stocks
            $this->annulerMouvements($data['details'], $dateHeure);

            // 1.bis Annulation des mouvements pneus (par numéro de série) avant suppression
            $model->load(['details.pneuSeries']);
            $this->annulerMouvementsPneu($model->details, $dateHeure);

            // 2. Supprimer d'abord les séries de pneus liées aux détails, puis les détails
            try {
                $detailIds = collect($model->details)->pluck('id')->all();
                $this->pneuSerieService->deleteByApproDetailIds($detailIds);
            } catch (\Throwable $e) {
            }
            $this->articleApproDetailsService->deleteByArticleAppro($data['id']);

            // 3. Suppression de l'approvisionnement
            parent::delete($model);

            DB::commit();
            return $this->successResponse('SUPPRESSION TERMINEE AVEC SUCCES');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('ERREUR LORS DE LA SUPPRESSION', $e);
        }
    }

    /**
     * Enregistre un nouvel approvisionnement avec ses détails et mouvements.
     */
    public function save(mixed $validated): array
    {
        DB::beginTransaction();
        try {
            $details = $validated['details'];
            $validated['user_id'] = Auth::id();
            $dateHeure = now();
            $majPrixArticle = (bool) ($validated['maj_prix_article'] ?? false);

            // 1. Création ou mise à jour du fournisseur
            $fournisseur = $this->saveFournisseur($validated['nom_fournisseur']);
            $validated['fournisseur_id'] = $fournisseur['element']->id;
            $validated['date_heure_enregistrement'] = $dateHeure;
            unset($validated['details'], $validated['nom_fournisseur'], $validated['maj_prix_article']);

            // 2. Insertion de l'approvisionnement
            $appro = $this->saveApprovisionnement($validated);

            // 3. Insertion des détails et mouvements
            if (!empty($details)) {
                $this->saveApproDetails($appro['element']->id, $details, $dateHeure, $validated['date_appro'], $majPrixArticle);
            }

            DB::commit();
            return $this->successResponse('INSERTION TERMINEE AVEC SUCCES');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('ERREUR LORS DE L\'INSERTION', $e);
        }
    }

    /**
     * Modifie un approvisionnement existant.
     */
    public function edit($model, array $validated): array
    {
        DB::beginTransaction();
        try {
            $dateHeure = now();
            $validated['user_id'] = Auth::id();
            $data = $model->load(['details'])->toArray();
            $majPrixArticle = (bool) ($validated['maj_prix_article'] ?? false);

            // 1. Annulation des anciens mouvements et suppression des anciens détails
            $this->annulerMouvements($data['details'], $dateHeure);
            // Annuler également les mouvements pneus existants
            $model->load(['details.pneuSeries']);
            $this->annulerMouvementsPneu($model->details, $dateHeure);
            // Supprimer séries de pneus liées aux anciens détails, puis les détails
            try {
                $detailIds = collect($model->details)->pluck('id')->all();
                $this->pneuSerieService->deleteByApproDetailIds($detailIds);
            } catch (\Throwable $e) {
            }
            $this->articleApproDetailsService->deleteByArticleAppro($data['id']);

            // 2. Mise à jour du fournisseur
            $fournisseur = $this->saveFournisseur($validated['nom_fournisseur']);
            $validated['fournisseur_id'] = $fournisseur['element']->id;

            $details = $validated['details'];
            unset($validated['details'], $validated['nom_fournisseur'], $validated['maj_prix_article']);

            // 3. Mise à jour de l’approvisionnement
            $appro = $this->updateApprovisionnement($model, $validated);

            // 4. Réinsertion des nouveaux détails
            if (!empty($details)) {
                $this->saveApproDetails($appro['element']->id, $details, $dateHeure, $validated['date_appro'], $majPrixArticle);
            }

            DB::commit();
            return $this->successResponse('MODIFICATION TERMINEE AVEC SUCCES');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('ERREUR LORS DE LA MODIFICATION', $e);
        }
    }

    /**
     * Crée ou met à jour un fournisseur en base.
     */
    private function saveFournisseur(string $nomFournisseur): array
    {
        return $this->fournisseurService->updateOrCreateStock($nomFournisseur);
    }

    /**
     * Insère un nouvel approvisionnement.
     */
    private function saveApprovisionnement(array $data): array
    {
        return parent::create($data);
    }

    /**
     * Met à jour un approvisionnement existant.
     */
    private function updateApprovisionnement($model, array $data): array
    {
        return parent::update($model, $data);
    }

    /**
     * Enregistre les détails d’un approvisionnement et génère les mouvements correspondants.
     */
    private function saveApproDetails(int $approId, array $details, $dateHeure, $dateAppro, bool $majPrixArticle = false): void
    {
        foreach ($details as $detail) {
            $detail['article_approvisionnement_id'] = $approId;

            // Insertion du détail
            $created = $this->articleApproDetailsService->create($detail);
            $detailId = is_array($created) && isset($created['element']) ? ($created['element']->id ?? null) : (is_object($created) ? ($created->id ?? null) : null);

            // Mise à jour du prix unitaire de l'article si demandé
            if ($majPrixArticle && isset($detail['article_id']) && isset($detail['prix_unitaire'])) {
                try {
                    $article = Article::find($detail['article_id']);
                    if ($article) {
                        $article->valeur = $detail['prix_unitaire'];
                        $article->save();
                    }
                } catch (\Throwable $e) {
                    // ignore update failure to not block the transaction; business log could be added
                }
            }

            // Création du mouvement correspondant
            $mouvement = [
                'date_mvt' => Carbon::parse($dateAppro)->format('Y-m-d'),
                'article_id' => $detail['article_id'],
                'magasin_id' => $detail['magasin_id'],
                'user_id' => Auth::id(),
                'date_heure_enregistrement' => $dateHeure,
                'operation_mvt' => 'APPROVISIONNEMENT',
                'qte_mvt' => $detail['quantite'],
            ];

            $this->articleMouvementService->entrerMouvement($mouvement);

            // Enregistrement des numéros de série pour les pneus
            if (
                (isset($detail['type_article']) && strtolower((string) $detail['type_article']) === 'pneu')
                && !empty($detail['numeros_serie']) && is_array($detail['numeros_serie'])
            ) {
                foreach ($detail['numeros_serie'] as $sn) {
                    $numero = $sn['numero_serie'] ?? null;
                    $etat = $sn['etat'] ?? null;
                    try {
                        $serieCreated = $this->pneuSerieService->create([
                            'numero_serie' => $numero,
                            'etat' => $etat,
                            'article_id' => $detail['article_id'],
                            'article_appro_detail_id' => $detailId,
                            'article_approvisionnement_id' => $approId,
                        ]);

                        $serieId = is_array($serieCreated) && isset($serieCreated['element'])
                            ? ($serieCreated['element']->id ?? null)
                            : (is_object($serieCreated) ? ($serieCreated->id ?? null) : null);

                        // Créer un mouvement pneu pour chaque numéro de série (entrée)
                        $pneuMvt = [
                            'date_mvt' => Carbon::parse($dateAppro)->format('Y-m-d'),
                            'article_id' => $detail['article_id'],
                            'magasin_id' => $detail['magasin_id'],
                            'user_id' => Auth::id(),
                            'date_heure_enregistrement' => $dateHeure,
                            'type_mvt' => 'ENTREE',
                            'reference_mvt' => 'APPROVISIONNEMENT',
                            'pneu_serie_id' => $serieId,
                            'commentaire' => $etat ? ('etat: ' . $etat) : null,
                        ];
                        $this->pneuMouvementService->create($pneuMvt);
                    } catch (\Throwable $e) {
                    }
                }
            }
        }
    }

    /**
     * Annule les mouvements d’approvisionnement pour mise à jour du stock.
     */
    private function annulerMouvements(array $details, $dateHeure): void
    {
        foreach ($details as $detail) {
            $mouvement = [
                'data_mvt' => now()->format('Y-m-d'),
                'article_id' => $detail['article_id'],
                'magasin_id' => $detail['magasin_id'],
                'user_id' => Auth::id(),
                'date_heure_enregistrement' => $dateHeure,
                'operation_mvt' => 'ANNULATION APPROVISIONNEMENT',
                'qte_mvt' => $detail['quantite'],
            ];

            $this->articleMouvementService->sortieMouvement($mouvement);
        }
    }

    //Annule les mouvements approvisionnement pneu, sortie par numéro de série
    private function annulerMouvementsPneu($details, $dateHeure): void
    {
        foreach ($details as $detail) {
            $pneuSeries = collect($detail->pneuSeries ?? []);
            if ($pneuSeries->isEmpty()) {
                continue;
            }
            foreach ($pneuSeries as $serie) {
                $pneuMvt = [
                    'date_mvt' => now()->format('Y-m-d'),
                    'article_id' => $detail->article_id,
                    'magasin_id' => $detail->magasin_id,
                    'user_id' => Auth::id(),
                    'date_heure_enregistrement' => $dateHeure,
                    'type_mvt' => 'SORTIE',
                    'reference_mvt' => 'ANNULATION APPROVISIONNEMENT',
                    'pneu_serie_id' => $serie->id ?? null,
                    'commentaire' => $serie->etat ? ('etat: ' . $serie->etat) : null,
                ];
                $this->pneuMouvementService->create($pneuMvt);
            }
        }
    }

    /**
     * Prépare les données pour l'édition en injectant les numéros de série pneus dans les détails
     */
    public function formatSeriePneuEdition(ArticleApprovisionnement $appro): array
    {
        $appro->load(['details.article', 'details.pneuSeries']);
        $data = $appro->toArray();

        // Identifier ids articles de type pneu
        $pneuIds = $appro->details
            ->filter(fn($d) => strtolower($d->article->type_article ?? '') === 'pneu')
            ->pluck('article_id')
            ->unique()
            ->values();

        if ($pneuIds->isEmpty()) return $data;


        // Injecter numéros de série par detail
        $data['details'] = collect($appro->details)->map(function ($d) {
            $row = $d->toArray();
            $row['numeros_serie'] = collect($d->pneuSeries)->map(fn($s) => [
                'numero_serie' => $s->numero_serie,
                'etat' => $s->etat,
            ])->values()->all();
            if (!empty($row['numeros_serie']))  $row['type_article'] = 'pneu';
            
            return $row;
        })->all();

        return $data;
    }


}
