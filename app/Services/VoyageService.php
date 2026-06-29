<?php

namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Repositories\VoyageRepository;
use App\Services\Base\BaseService as BaseBaseService;
use App\Models\Voyage;
use App\Services\VoyageMarchandiseService;
use App\Services\VoyageChargeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\VoyageAffectation;

class VoyageService extends BaseBaseService
{
    protected Voyage $voyage;
    protected VoyageRepository $voyageRepository;
    protected array $relation = ['reservation.client', 'vehicule', 'chauffeur', 'remorque', 'voyageMarchandises', 'voyageCharges', 'carburantTransactions', 'voyageAffectation'];
    protected array $scope = [
        'filter' => 'search',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date',
        'filteretatfacture' => 'etat_facture',
        'filterclient' => 'client_id',
        'filtervehicule' => 'vehicule_id',
        'filteruser' => 'user_id',
        'filterchauffeur' => 'chauffeur_id',
        'filterstatut' => 'statut',
    ];
    protected VoyageMarchandiseService $voyageMarchandiseService;
    protected VoyageChargeService $voyageChargeService;
    protected VoyageAffectationService $voyageAffectationServ;


    public function getAll(array $filters = [])
    {
        return parent::getAll($filters);
    }
    public function getRelation()
    {
        return $this->relation;
    }
    public function __construct(
        Voyage $voyage,
        VoyageRepository $voyageRepository,
        VoyageMarchandiseService $voyageMarchandiseService,
        VoyageChargeService $voyageChargeService,
        VoyageAffectationService $voyageAffectationService,
        protected readonly ReservationRepository $reservationRepository
    ) {
        $this->voyage = $voyage;

        $this->voyageRepository = $voyageRepository;
        $this->voyageMarchandiseService = $voyageMarchandiseService;
        $this->voyageChargeService = $voyageChargeService;
        $this->voyageAffectationServ = $voyageAffectationService;
        parent::__construct($voyageRepository);

        $this->repository->setDefaultOrder('date_voyage', 'desc');

    }

    public function getNumeroVoyage(): int
    {
        $first_date = Carbon::now()->startOfMonth()->toDateString();
        $last_date = Carbon::now()->endOfMonth()->toDateString();
        $lastVoyage = $this->voyageRepository->findLastVoyage($first_date, $last_date);
        if ($lastVoyage) {
            return $lastVoyage->count_numero_voyage + 1;
        }
        // Si aucun voyage ce mois-ci, prendre le dernier voyage tous mois confondus
        $lastVoyageEver = $this->voyage->orderBy('count_numero_voyage', 'DESC')->first();
        return $lastVoyageEver ? $lastVoyageEver->count_numero_voyage + 1 : 1;
    }

    public function generateNumeroVoyage(?int $count = null): array
    {
        if ($count === null) {
            $count = $this->getNumeroVoyage();
        }
        $moisAnnee = Carbon::now()->format('m-Y');
        $numero = sprintf('VOY-%03d/%s', $count, $moisAnnee);
        // S'assurer que le numéro est unique
        while ($this->voyage->where('numero_voyage', $numero)->exists()) {
            $count++;
            $numero = sprintf('VOY-%03d/%s', $count, $moisAnnee);
        }
        return [
            'count_numero_voyage' => $count,
            'numero_voyage' => $numero
        ];
    }

    public function create(array $validated): array
    {
        $numero = $this->generateNumeroVoyage();
        $validated['numero_voyage'] = $numero['numero_voyage'];
        $validated['count_numero_voyage'] = $numero['count_numero_voyage'];
        $tarif_ht_total = $validated['tarif_ht_total'] ?? 0;
        $mobilisation = $validated['mobilisation'] ?? 0;
        $nbr_jour = $validated['nbr_jour'] ?? 0;
        $valeur_tva = $validated['valeur_tva'] ?? 0;
        $remise = $validated['remise'] ?? 0;
        // Calcul du montant_ht (baseHT) avant remise
        $montant_ht_avant_remise = $tarif_ht_total + ($mobilisation * $nbr_jour);
        $montant_ht = max($montant_ht_avant_remise - $remise, 0);
        // Calcul du montant_tva
        $montant_tva = ($valeur_tva > 0) ? $montant_ht * ($valeur_tva / 100) : 0;
        // Calcul du montant_ttc
        $tarif_ttc = $montant_ht + $montant_tva;
        $validated['montant_ht'] = $montant_ht;
        $validated['montant_tva'] = $montant_tva;
        $validated['tarif_ttc'] = $tarif_ttc;
        $validated['montant'] = $tarif_ttc;
        $validated['valeur_tva'] = $valeur_tva;

        $success = parent::create($validated);
        if (!$success) {
            return ['error' => true, 'message' => 'Échec de la création du voyage dans le service de base.'];
        }
        $createdVoyage = $this->voyage->where('numero_voyage', $validated['numero_voyage'])->first();
        if (!$createdVoyage) {
            return ['error' => true, 'message' => 'Voyage créé, mais impossible de le récupérer.'];
        }
        //creation de l'affectation
        $this->voyageAffectationServ->create([
            'voyage_id' => $createdVoyage->id,
            'date' => $createdVoyage->date_voyage,
            'vehicule_id' => $validated['vehicule_id'],
            'chauffeur_id' => $validated['chauffeur_id'],
            'remorque_id' => $validated['remorque_id'],
        ]);
        return ['error' => false, 'message' => 'Voyage créé avec succès.', 'data' => $createdVoyage];
    }

    public function update($voyage, array $validated): array
    {

        $tarif_ht_total = $validated['tarif_ht_total'] ?? 0;
        $mobilisation = $validated['mobilisation'] ?? 0;
        $nbr_jour = $validated['nbr_jour'] ?? 0;
        $valeur_tva = $validated['valeur_tva'] ?? 0;
        $remise = $validated['remise'] ?? 0;
        // Calcul du montant_ht (baseHT) avant remise
        $montant_ht_avant_remise = $tarif_ht_total + ($mobilisation * $nbr_jour);
        $montant_ht = max($montant_ht_avant_remise - $remise, 0);
        // Calcul du montant_tva
        $montant_tva = ($valeur_tva > 0) ? $montant_ht * ($valeur_tva / 100) : 0;
        // Calcul du montant_ttc
        $tarif_ttc = $montant_ht + $montant_tva;
        $validated['remise'] = $remise;
        $validated['montant_ht'] = $montant_ht;
        $validated['montant_tva'] = $montant_tva;
        $validated['tarif_ttc'] = $tarif_ttc;
        $validated['montant'] = $tarif_ttc;
        $validated['valeur_tva'] = $valeur_tva;

        $voyage->update($validated);
        $this->voyageAffectationServ->update(
            $voyage->voyageAffectation,[
                'voyage_id' => $voyage->id,
                'date' => $voyage->date_voyage,
                'vehicule_id' => $validated['vehicule_id'],
                'chauffeur_id' => $validated['chauffeur_id'],
                'remorque_id' => $validated['remorque_id'],
            ]
            );
        return ['error' => false, 'message' => 'Voyage mis à jour avec succès.', 'data' => $voyage];
    }

    public function delete($voyage)
    {
        $voyage->delete();
        return ['error' => false, 'message' => 'Voyage supprimé avec succès.'];
    }

    public function getAllLieuOptions()
    {
        $departs = $this->voyageRepository->fetchDistinctByColumn('depart');
        $destinations = $this->voyageRepository->fetchDistinctByColumn('destination');
        return $departs
            ->concat($destinations)
            ->unique()
            ->map(fn($l) => ['value' => $l, 'label' => $l])
            ->sortBy('label')
            ->values()
            ->all();
    }


    /**
     * Méthode pour regroupé tout les recap nécessaire pour le dashboard
     * @param array $filtre
     * @return void
     */
    public function dashboard(array $filtre): array
    {
        return [
            'totalVoyage' => $this->getTotalVoyage($filtre),
            'recapVoyageStatutReservation' => $this->recapVoyageStatutReservation($filtre),
            'listeDestination' => $this->getDestination($filtre),
            'client' => $this->recapVoyageClient($filtre),
            'recapByAnnee' => $this->recapByAnnee($filtre),
        ];
    }

    /**
     * Récupère le nombre total de voyages en fonction des filtres.
     * @param array $filtre
     * @return int
     */
    private function getTotalVoyage(array $filtre) :int
    {
        $data = parent::countElement($filtre);
        return $data;
    }

    /**
     * Récupère le récapitulatif des voyages en fonction de leur statut de réservation.
     * @param array $filtre
     * @return array
     */
    private function recapVoyageStatutReservation(array $filtre): array
    {
        $data = parent::getAll($filtre)->all();
        return array_reduce($data, function ($carry, $item) {
            if ($item->facture_client_id > 0) {
                $carry['voyageFacturer']++;
            } else { $carry['voyageNonFacturer']++;
            }
            return $carry;
        }, [
            'voyageFacturer' => 0,
            'voyageNonFacturer' => 0,
        ]);
    }

    private function getDestination(array $filters)
    {

      $data = $this->repository->getVoyageParDestination($filters, $this->scope);
      return $data;
    }

    private function recapVoyageClient(array $filtre)
    {
        $data = $this->repository->recapVoyageClient($filtre, $this->scope);
        return $data;
    }

    private function recapByAnnee($filtre)
    {
        $data = $this->repository->getVoyagesParAnnee($filtre, $this->scope);
        return $data;
    }
    public function planningVoyage(array $filtre = [])
    {
        $voyages = $this->getAll($filtre);
        // Map
        return $voyages->map(function ($v) {
            return [
                'id' => $v->id,
                'libelle' => $v->numero_voyage,
                'description' => null,
                'date_maintenance' => Carbon::parse($v->date_voyage)->format('d-m-Y'),
                'background' => '#1890ff',
                'text_color' => '#ffffff',
            ];
        })->all();
    }

}
