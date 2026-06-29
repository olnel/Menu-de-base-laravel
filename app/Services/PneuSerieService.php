<?php

namespace App\Services;

use App\Repositories\PneuSerieRepository;
use App\Services\Base\BaseService;
use Illuminate\Database\Eloquent\Model;

class PneuSerieService extends BaseService
{
    protected $repository;

    public function __construct(PneuSerieRepository $pneuSerieRepository)
    {
        $this->repository = $pneuSerieRepository;
        parent::__construct($pneuSerieRepository);
    }

    // Your service methods go here
    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('numero_serie');
    }

    public function deleteByApproDetailIds(array $detailIds): int
    {
        return $this->repository->deleteByApproDetailIds($detailIds);
    }
    public function getSeriesByArticle($articleId)
    {
        $series = $this->repository->getSeriesByArticle($articleId);
        return $series
            ->map(function ($serie) {
                $detail = $serie->approDetail;
                $appro = $detail?->article_approvisionnement;
                return [
                    'id' => $serie->id,
                    'numero_serie' => $serie->numero_serie,
                    'etat' => $serie->etat,
                    'date_appro' => $appro?->date_appro,
                    'nom_magasin' => $detail?->magasin?->nom_magasin,
                    'nom_fournisseur' => $appro?->fournisseur?->nom_fournisseur,
                ];
            });
    }

    public function getSeriesDetailsByArticle($articleId)
    {
        $series = $this->repository->getSeriesWithDetailsForArticle($articleId);
        return $series->map(function ($serie) {
            $detail = $serie->approDetail;
            $appro  = $detail?->article_approvisionnement;
            return [
                'id'                => $serie->id,
                'numero_serie'      => $serie->numero_serie,
                'etat'              => $serie->etat,
                'occupe'            => (bool) $serie->occupe,
                'is_existe'         => (bool) $serie->is_existe,
                'position_actuel'   => $serie->position_actuel,
                'position_initial'  => $serie->position_initial,
                'date_montage'      => $serie->date_montage,
                'date_hors_service' => $serie->date_hors_service,
                'duree_vie_previsionnel' => $serie->duree_vie_previsionnel,
                'total_km'          => $serie->total_km,
                'alert_niveau'      => $serie->alert_niveau,
                'vehicule'          => $serie->vehicule ? [
                    'id'              => $serie->vehicule->id,
                    'immatriculation' => $serie->vehicule->immatriculation,
                    'marque'          => $serie->vehicule->marque,
                    'modele'          => $serie->vehicule->modele,
                ] : null,
                'remorque'          => $serie->remorque ? [
                    'id'              => $serie->remorque->id,
                    'numero_remorque' => $serie->remorque->numero_remorque,
                ] : null,
                'date_appro'        => $appro?->date_appro,
                'nom_magasin'       => $detail?->magasin?->nom_magasin,
                'nom_fournisseur'   => $appro?->fournisseur?->nom_fournisseur,
                'voyage_count'      => $serie->voyages->count(),
            ];
        });
    }


    public function getPneusNonAssignes(?string $search = null)
    {
        return $this->repository->getPneusNonAssignes($search, 1);
    }

    public function getPneusDisponibles(?string $search = null, array $exclude = [], int $limit = 10)
    {
        return $this->repository->getPneusDisponibles($search, $exclude, $limit);
    }

    public function getAssignedNumeroSeriesByVehicule(int $vehiculeId): array
    {
        return $this->repository->getAssignedNumeroSeriesByVehicule($vehiculeId);
    }

    public function getAssignedNumeroSeriesByRemorque(int $remorqueId): array
    {
        return $this->repository->getAssignedNumeroSeriesByRemorque($remorqueId);
    }

    //Récupère une série par son numero_serie
    public function getByNumeroSerie(string $numeroSerie): ?Model
    {
        return $this->repository->findElement(['numero_serie' => $numeroSerie]);
    }

    //Assigne une série de pneu à un véhicule par numero_serie
    public function assignToVehiculeByNumeroSerie(string $numeroSerie, ?int $vehiculeId = null, ?int $remorqueId = null): void
    {
        $serie = $this->repository->findElement(['numero_serie' => $numeroSerie]);
        if (!$serie) { return;}
        $serie->vehicule_id = $vehiculeId;
        $serie->remorque_id = $remorqueId;
        $serie->save();
    }

    public function getByVehicule(int $vehiculeId)
    {
        return $this->repository->getByVehicule($vehiculeId);
    }

    public function getByRemorque(int $remorqueId)
    {
        return $this->repository->getByRemorque($remorqueId);
    }

    public function searchPneus(
        ?string $search,
        ?int    $vehiculeId,
        ?int    $remorqueId,
        ?int    $magasinId,
        int     $limit = 10
    ) {
        return $this->repository->searchPneus($search, $vehiculeId, $remorqueId, $magasinId, $limit);
    }

    //Détache une série du véhicule/remorque par numero_serie
    public function detachFromVehiculeByNumeroSerie(string $numeroSerie): void
    {
        $serie = $this->repository->findElement(['numero_serie' => $numeroSerie]);
        if (!$serie) { return; }
        $serie->vehicule_id = null;
        $serie->remorque_id = null;
        $serie->save();
    }

    //Met à jour uniquement la remorque d'une série déjà affectée au véhicule par numero_serie
    public function updateRemorqueByNumeroSerie(string $numeroSerie, ?int $remorqueId = null): void
    {
        $serie = $this->repository->findElement(['numero_serie' => $numeroSerie]);
        if (!$serie) { return; }
        $serie->remorque_id = $remorqueId;
        $serie->save();
    }
}
