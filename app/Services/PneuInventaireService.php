<?php

namespace App\Services;

use App\Models\PneuSerie;
use App\Repositories\PneuInventaireRepository;
use App\Repositories\PneuMouvementRepository;
use App\Repositories\PneuSerieRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PneuInventaireService extends BaseService
{
    protected $repository;

    protected array $relation = ['vehicule', 'remorque', 'magasin', 'user', 'article'];

    protected array $scope = [
        'filter'          => 'search',
        'magasin'         => 'magasin_id',
        'article'         => 'article_id',
        'filterdatestart' => 'start_date',
        'filterdateend'   => 'end_date',
    ];

    public function __construct(
        PneuInventaireRepository $inventaireRepository,
        PneuSerieRepository      $pneuSerieRepository,
        PneuMouvementRepository  $pneuMouvementRepository,
    )
    {
        $this->repository = $inventaireRepository;
        $this->pneuSerieRepository = $pneuSerieRepository;
        $this->pneuMouvementRepository = $pneuMouvementRepository;
        parent::__construct($inventaireRepository);
    }

    private PneuSerieRepository $pneuSerieRepository;
    private PneuMouvementRepository $pneuMouvementRepository;

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('reference');
    }

    /**
     * Récupère les pneus existants d'un magasin pour pré-remplir l'inventaire.
     */
    public function getPneusByMagasin(int $magasinId): array
    {
        $pneus = PneuSerie::query()
            ->whereHas('approDetail', fn($q) => $q->where('magasin_id', $magasinId))
            ->with([
                'article',
                'approDetail.article',
                'approDetail.magasin',
                'approDetail.article_approvisionnement.fournisseur',
                'vehicule',
                'remorque',
            ])
            ->get()
            ->map(fn($pneu) => [
                'id' => $pneu->id,
                'numero_serie' => $pneu->numero_serie,
                'etat' => $pneu->etat,
                'is_existe' => (bool)($pneu->is_existe ?? true),
                'occupe' => (bool)($pneu->occupe ?? false),
                'type' => $pneu->type,
                'article_id' => $pneu->article_id,
                'article_reference' => $pneu->article?->reference ?? $pneu->approDetail?->article?->reference,
                'article_designation' => $pneu->article?->designation ?? $pneu->approDetail?->article?->designation,
                'date_appro' => $pneu->approDetail?->article_approvisionnement?->date_appro,
                'fournisseur' => $pneu->approDetail?->article_approvisionnement?->fournisseur?->nom_fournisseur,
                'inventorie' => !is_null($pneu->pneu_inventaire_id),
                'vehicule_id' => $pneu->vehicule_id,
                'vehicule_label' => $pneu->vehicule?->immatriculation,
                'remorque_id' => $pneu->remorque_id,
                'remorque_label' => $pneu->remorque?->numero_remorque,
            ]);

        return $this->successResponse('Pneus récupérés', $pneus->toArray());
    }

    /**
     * Compte le stock théorique (pneus en magasin via approvisionnement).
     */
    public function getStockTheoriquePneus(int $magasinId): int
    {
        return PneuSerie::query()
            ->whereHas('approDetail', fn($q) => $q->where('magasin_id', $magasinId))
            ->count();
    }

    /**
     * Enregistre un nouvel inventaire pneu (non modifiable après création).
     */
    public function save(array $validated): array
    {
        try {
            DB::beginTransaction();

            $magasinId      = $validated['magasin_id'];
            $dateInventaire = $validated['date_inventaire'];
            $remarque       = $validated['remarque'] ?? null;
            $userId         = Auth::id();
            $details        = $validated['details'] ?? [];

            foreach ($details as $detail) {
                $vehiculeId = $detail['vehicule_id'] ?? null;
                $remorqueId = $detail['remorque_id'] ?? null;
                $isExiste   = isset($detail['is_existe']) ? (bool) $detail['is_existe'] : true;
                $occupe     = !is_null($vehiculeId) || !is_null($remorqueId);
                $type       = $vehiculeId ? 'vehicule' : ($remorqueId ? 'remorque' : null);

                $inventaire = $this->repository->create([
                    'magasin_id'      => $magasinId,
                    'user_id'         => $userId,
                    'remarque'        => $remarque,
                    'date_inventaire' => $dateInventaire,
                    'article_id'      => $detail['article_id'] ?? null,
                    'numero_serie'    => $detail['numero_serie'],
                    'vehicule_id'     => $vehiculeId,
                    'remorque_id'     => $remorqueId,
                    'is_existe'       => $isExiste,
                    'occupe'          => $occupe,
                    'etat'            => $detail['etat'] ?? null,
                    'type'            => $type,
                ]);

                $this->processDetail($detail, $inventaire->id, $magasinId, $dateInventaire, $userId);
            }

            DB::commit();

            $nbPneus = count($details);
            return $this->successResponse(
                "Inventaire enregistré — {$nbPneus} pneu(s) traité(s).",
                ['magasin_id' => $magasinId, 'date_inventaire' => $dateInventaire, 'details_count' => $nbPneus]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse("Erreur lors de l'inventaire pneu", $e);
        }
    }

    /**
     * Un inventaire ne peut pas être modifié.
     */
    public function update($model, array $validated): array
    {
        return $this->errorResponse(
            "Un inventaire pneu ne peut pas être modifié.",
            new \RuntimeException("Modification interdite sur pneu_inventaire #{$model->id}")
        );
    }

    // -------------------------------------------------------------------------
    // Privé
    // -------------------------------------------------------------------------

    private function processDetail(array $detail, int $inventaireId, int $magasinId, string $dateInventaire, int $userId): void
    {
        $numeroSerie = $detail['numero_serie'];
        $etat = $detail['etat'] ?? null;
        $isExiste = isset($detail['is_existe']) ? (bool)$detail['is_existe'] : true;
        $vehiculeId = $isExiste ? ($detail['vehicule_id'] ?? null) : null;
        $remorqueId = $isExiste ? ($detail['remorque_id'] ?? null) : null;
        $occupe = !is_null($vehiculeId) || !is_null($remorqueId);
        $type = $vehiculeId ? 'vehicule' : ($remorqueId ? 'remorque' : null);
        $articleId = $detail['article_id'] ?? null;

        $pneuSerie = $this->upsertPneuSerie(
            $numeroSerie, $etat, $isExiste, $occupe, $type,
            $vehiculeId, $remorqueId, $inventaireId, $articleId
        );

        $this->createMouvement(
            $pneuSerie, $inventaireId, $magasinId,
            $dateInventaire, $userId, $isExiste, $etat, $vehiculeId, $remorqueId
        );
    }

    /**
     * Insère ou met à jour la série de pneu dans pneu_series.
     */
    private function upsertPneuSerie(
        string  $numeroSerie,
        ?string $etat,
        bool    $isExiste,
        bool    $occupe,
        ?string $type,
        ?int    $vehiculeId,
        ?int    $remorqueId,
        int     $inventaireId,
        ?int    $articleId
    ): PneuSerie
    {
        $pneuSerie = PneuSerie::query()->where('numero_serie', $numeroSerie)->first();

        if ($pneuSerie) {
            $pneuSerie->etat = $etat;
            $pneuSerie->is_existe = $isExiste;
            $pneuSerie->occupe = $occupe;
            $pneuSerie->type = $type;
            $pneuSerie->vehicule_id = $vehiculeId;
            $pneuSerie->remorque_id = $remorqueId;
            $pneuSerie->pneu_inventaire_id = $inventaireId;
            if (empty($pneuSerie->article_id) && !empty($articleId)) {
                $pneuSerie->article_id = $articleId;
            }
            $pneuSerie->save();
        } else {
            $pneuSerie = $this->pneuSerieRepository->create([
                'numero_serie' => $numeroSerie,
                'etat' => $etat,
                'is_existe' => $isExiste,
                'occupe' => $occupe,
                'type' => $type,
                'vehicule_id' => $vehiculeId,
                'remorque_id' => $remorqueId,
                'pneu_inventaire_id' => $inventaireId,
                'article_id' => $articleId,
            ]);
        }

        return $pneuSerie;
    }

    /**
     * Crée un mouvement pneu pour tracer l'état de chaque série à l'inventaire.
     */
    private function createMouvement(
        PneuSerie $pneuSerie,
        int       $inventaireId,
        int       $magasinId,
        string    $dateInventaire,
        int       $userId,
        bool      $isExiste,
        ?string   $etat,
        ?int      $vehiculeId,
        ?int      $remorqueId
    ): void
    {
        $this->pneuMouvementRepository->create([
            'pneu_serie_id' => $pneuSerie->id,
            'pneu_inventaire_id' => $inventaireId,
            'article_id' => $pneuSerie->article_id,
            'magasin_id' => $magasinId,
            'user_id' => $userId,
            'numero_serie' => $pneuSerie->numero_serie,
            'is_existe' => $isExiste,
            'etat' => $etat,
            'vehicule_id' => $vehiculeId,
            'remorque_id' => $remorqueId,
            'type_mvt' => 'INVENTAIRE',
            'reference_mvt' => 'INV-PNEU-' . $inventaireId . '-' . $pneuSerie->id,
            'date_mvt' => $dateInventaire,
            'date_heure_enregistrement' => now(),
        ]);
    }
}
