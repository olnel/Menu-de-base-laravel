<?php

namespace App\Repositories;

use App\Models\PneuSerie;
use Illuminate\Database\Eloquent\Model;

class PneuSerieRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(PneuSerie $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }
    public function getModel(): Model
    {
        return $this->model;
    }
    public function deleteByApproDetailIds(array $detailIds): int
    {
        if (empty($detailIds)) {
            return 0;
        }
        return $this->model->whereIn('article_appro_detail_id', $detailIds)->delete();
    }


    public function getPneusNonAssignes(?string $search = null, int $limit = 10)
    {
        $query = $this->model->whereNull('vehicule_id')->whereNull('remorque_id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_serie', 'like', "%{$search}%")
                    ->orWhere('etat', 'like', "%{$search}%");
            });
        }

        return $query->limit($limit)->get();
    }

    public function getAssignedNumeroSeriesByVehicule(int $vehiculeId): array
    {
        return $this->model->newQuery()
            ->where('vehicule_id', $vehiculeId)
            ->pluck('numero_serie')
            ->all();
    }

    public function getAssignedNumeroSeriesByRemorque(int $remorqueId): array
    {
        return $this->model->newQuery()
            ->where('remorque_id', $remorqueId)
            ->pluck('numero_serie')
            ->all();
    }

    public function getByVehicule(int $vehiculeId)
    {
        return $this->model
            ->where('vehicule_id', $vehiculeId)
            ->with(['article'])
            ->get();
    }

    public function getByRemorque(int $remorqueId)
    {
        return $this->model
            ->where('remorque_id', $remorqueId)
            ->with(['article'])
            ->get();
    }

    public function searchPneus(
        ?string $search,
        ?int    $vehiculeId,
        ?int    $remorqueId,
        ?int    $magasinId,
        int     $limit = 10
    ) {
        $query = $this->model->newQuery()->with(['article']);

        if ($vehiculeId) {
            $query->where('vehicule_id', $vehiculeId);
        } elseif ($remorqueId) {
            $query->where('remorque_id', $remorqueId);
        } elseif ($magasinId) {
            $query->whereNull('vehicule_id')
                  ->whereNull('remorque_id')
                  ->where(function ($q) use ($magasinId) {
                      $q->whereHas('approDetail', fn($q2) => $q2->where('magasin_id', $magasinId))
                        ->orWhereNull('article_appro_detail_id');
                  });
        }

        if ($search) {
            $query->where('numero_serie', 'like', "%{$search}%");
        }

        return $query->limit($limit)->get();
    }

    public function getPneusDisponibles(?string $search = null, array $exclude = [], int $limit = 10)
    {
        $query = $this->model->newQuery()
            ->whereNull('vehicule_id')
            ->whereNull('remorque_id');

        if (!empty($search)) {
            $query->where('numero_serie', 'like', "%{$search}%");
        }

        if (!empty($exclude)) {
            $query->whereNotIn('numero_serie', $exclude);
        }

        return $query->limit($limit)->get(['id', 'numero_serie', 'etat', 'type', 'date_montage']);
    }

    /**
     * Retrieve all series for a given article with required relations eager loaded.
     */
    public function getSeriesByArticle($articleId)
    {
        return $this->model
            ->where('article_id', $articleId)
            ->with([
                'approDetail.magasin',
                'approDetail.article_approvisionnement',
                'approDetail.article_approvisionnement.fournisseur',
            ])
            ->get();
    }

    /**
     * Séries avec toutes les relations nécessaires pour l'affichage détaillé.
     */
    public function getSeriesWithDetailsForArticle($articleId)
    {
        return $this->model
            ->where('article_id', $articleId)
            ->with([
                'vehicule',
                'remorque',
                'approDetail.magasin',
                'approDetail.article_approvisionnement.fournisseur',
                'voyages',
            ])
            ->orderByRaw("occupe ASC, etat ASC, numero_serie ASC")
            ->get();
    }
}
