<?php

namespace App\Repositories;

use App\Models\Evaluation;
use App\Models\FactureClient;
use App\Models\Reclamation;
use App\Models\Reservation;
use App\Utils\BaseQueryTemplate;
use Illuminate\Database\Eloquent\Model;

class PortailDashboardRepository extends BaseRepository
{
    protected Model $model;
    protected FactureClient $factureModel;
    protected Reclamation $reclamationModel;
    protected Evaluation $evaluationModel;

    public function __construct(Reservation $reservation, FactureClient $factureClient, Reclamation $reclamation, Evaluation $evaluation)
    {
        $this->model            = $reservation;
        $this->factureModel     = $factureClient;
        $this->reclamationModel = $reclamation;
        $this->evaluationModel  = $evaluation;
        parent::__construct($reservation);
    }

    public function getRecentes(int $clientId, int $limit = 5)
    {
        return $this->model->newQuery()
            ->where('client_id', $clientId)
            ->latest('date_reservation')
            ->limit($limit)
            ->get(['id', 'numero_reservation', 'date_reservation', 'lieu_chargement', 'lieu_livraison', 'etat_facture', 'nbr_vehicule']);
    }

    public function getFactureStats(int $clientId, ?string $startDate, ?string $endDate): array
    {
        $makeQuery = fn() => $this->factureModel->newQuery()
            ->where('client_id', $clientId)
            ->when($startDate, fn($q) => $q->where('date_facture', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where('date_facture', '<=', $endDate));

        return [
            'total'         => $makeQuery()->count(),
            'payees'        => $makeQuery()->where('statut_facture', 'Payée')->count(),
            'en_attente'    => $makeQuery()->whereNotIn('statut_facture', ['Payée'])->count(),
            'montant_ttc'   => (int) $makeQuery()->sum('montant_ttc'),
            'montant_paye'  => (int) $makeQuery()->sum('montant_payer'),
            'montant_reste' => (int) $makeQuery()->sum('montant_reste_a_payer'),
        ];
    }

    public function getRecentesFactures(int $clientId, int $limit = 3)
    {
        return $this->factureModel->newQuery()
            ->where('client_id', $clientId)
            ->latest('date_facture')
            ->limit($limit)
            ->get(['id', 'numero_facture', 'date_facture', 'statut_facture', 'montant_ttc', 'montant_payer', 'montant_reste_a_payer']);
    }

    public function getReclamationStats(int $clientId, ?string $startDate, ?string $endDate): array
    {
        $makeQuery = fn() => $this->reclamationModel->newQuery()
            ->where('client_id', $clientId)
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where('created_at', '<=', $endDate . ' 23:59:59'));

        return [
            'total'      => $makeQuery()->count(),
            'en_attente' => $makeQuery()->where('statut', 'en_attente')->count(),
            'en_cours'   => $makeQuery()->where('statut', 'en_cours')->count(),
            'resolue'    => $makeQuery()->where('statut', 'resolue')->count(),
            'rejetee'    => $makeQuery()->where('statut', 'rejetee')->count(),
        ];
    }

    public function getRecentesReclamations(int $clientId, int $limit = 3)
    {
        return $this->reclamationModel->newQuery()
            ->with(['voyage:id,numero_voyage'])
            ->where('client_id', $clientId)
            ->latest()
            ->limit($limit)
            ->get(['id', 'numero_reclamation', 'objet', 'categorie', 'statut', 'voyage_id', 'created_at']);
    }

    public function getEvaluationStats(int $clientId, ?string $startDate, ?string $endDate): array
    {
        $makeQuery = fn() => $this->evaluationModel->newQuery()
            ->where('client_id', $clientId)
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->where('created_at', '<=', $endDate . ' 23:59:59'));

        return [
            'total'        => $makeQuery()->count(),
            'note_moyenne' => round((float) $makeQuery()->avg('note'), 1),
            'tres_bonnes'  => $makeQuery()->whereIn('note', [4, 5])->count(),
            'a_ameliorer'  => $makeQuery()->whereIn('note', [1, 2])->count(),
        ];
    }

    public function getRecentesEvaluations(int $clientId, int $limit = 3)
    {
        return $this->evaluationModel->newQuery()
            ->with(['chauffeur:id,nom,prenom', 'voyage:id,numero_voyage,destination'])
            ->where('client_id', $clientId)
            ->latest()
            ->limit($limit)
            ->get(['id', 'numero_evaluation', 'note', 'commentaire', 'chauffeur_id', 'voyage_id', 'created_at']);
    }
}
