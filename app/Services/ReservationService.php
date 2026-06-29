<?php

namespace App\Services;

use App\Services\Base\BaseService;
use App\Models\Reservation;
use App\Models\Client;
use App\Models\Voyage;
use App\Repositories\ReservationRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationService extends BaseService
{
    protected Reservation $reservation;
    protected $reservationRepository;
    protected array $relation = ['client', 'voyages', 'user', 'voyages.vehicule.remorque'];
    protected $client;
    protected array $scope = ['filter' => 'search', 'filterdateend' => 'end_date', 'filterdatestart' => 'start_date','filterclient'=>'client_id'];
    protected VoyageService $voyageService;
    protected ClientService $clientService;
    protected Voyage $voyage;

    public function __construct(ReservationRepository $reservationRepository, Reservation $reservation, Client $client, VoyageService $voyageService, ClientService $clientService,Voyage $voyage)
    {
        $this->reservation = $reservation;
        $this->reservationRepository = $reservationRepository;
        $this->voyageService = $voyageService;
        $this->clientService = $clientService;
        $this->voyage = $voyage;
        parent::__construct($reservationRepository);
    }
    public function getAll(array $filters = [])
    {
        return parent::getAll($filters);
    }

    private function getNumeroReservation(): int
    {
        $lastReservation = $this->reservationRepository->findLastReservation();
        return $lastReservation ? $lastReservation->count_numero_reservation + 1 : 1;
    }

    public function generateNumeroReservation(): array
    {
        $count = $this->getNumeroReservation();
        $moisAnnee = Carbon::now()->format('m-Y');
        $numero = sprintf('RES-%03d/%s', $count, $moisAnnee);
        return [
            'count_numero_reservation' => $count,
            'numero_reservation' => $numero
        ];
    }

    public function create(array $validated): array
    {
        $numero = $this->generateNumeroReservation();
        $validated['numero_reservation'] = $numero['numero_reservation'];
        $validated['count_numero_reservation'] = $numero['count_numero_reservation'];
        $validated['date_reservation'] = Carbon::parse($validated['date_reservation']);
        if (!array_key_exists('user_id', $validated)) {
            $validated['user_id'] = Auth::id();
        }

        // Gérer le client par nom_client
        if (isset($validated['nom_client'])) {
            $clientResult = $this->clientService->findOrCreateByNomClient($validated['nom_client']);
            if ($clientResult['error']) {
                return $clientResult;
            }
            $validated['client_id'] = $clientResult['element']['id'];
            unset($validated['nom_client']);
        }

        $result = parent::create($validated);
        return ['error' => false, 'message' => 'Réservation créée avec succès.', 'element' => $result['element']];
    }

    public function update($reservation, array $validated): array
    {
        $validated['date_reservation'] = Carbon::parse($validated['date_reservation']);

        // Gérer le client par nom_client
        if (isset($validated['nom_client'])) {
            $clientResult = $this->clientService->findOrCreateByNomClient($validated['nom_client']);
            if ($clientResult['error']) {
                return $clientResult;
            }
            $validated['client_id'] = $clientResult['element']['id'];
            unset($validated['nom_client']);
        }

        $reservation->update($validated);
        return ['error' => false, 'message' => 'Réservation mise à jour avec succès.'];
    }
    public function delete($reservation)
    {
        DB::transaction(function () use ($reservation) {
            $reservation->delete();
        });
    }

    public function editStatutReservation($reservationId, array $voyages, $statut_facture, $factureInfo)
    {
        $reservation = $this->reservationRepository->find($reservationId);
        $reservation->load('voyages');
        $etat_facture = 'Non payée';


        if ($statut_facture !== 'Brouillon') {
            if (count($reservation->voyages) === count($voyages)) {
                $etat_facture = 'Payée';
            } else {
                $etat_facture = 'Partiellement payée';
            }
        }

        $reservation->etat_facture = $etat_facture;
        $reservation->facture_client_id = $factureInfo['element']->id ?? null;
        $reservation->save();
    }

    public function getAllLieuOptions()
    {
        $chargements = $this->reservationRepository->fetchDistinctByColumn('lieu_chargement');
        $livraisons = $this->reservationRepository->fetchDistinctByColumn('lieu_livraison');
        return $chargements
            ->concat($livraisons)
            ->unique()
            ->map(fn($l) => ['value' => $l, 'label' => $l])
            ->sortBy('label')
            ->values()
            ->all();
    }

    public function getDistinctLieuChargement()
    {
        return $this->reservationRepository->fetchDistinctByColumn('lieu_chargement')
            ->map(fn($l) => ['value' => $l, 'label' => $l])
            ->values();
    }
    public function getDistinctLieuLivraison()
    {
        return $this->reservationRepository->fetchDistinctByColumn('lieu_livraison')
            ->map(fn($l) => ['value' => $l, 'label' => $l])
            ->values();
    }


    public function dashboard(array $filtre)
    {
        return [
            'totalReservation' => $this->getTotalReservation($filtre),
            'StatutReservation' => $this->recapStatutReservation($filtre),
            'destination' => $this->getDestination($filtre),
            'client' => $this->recapVoyageClient($filtre),
        ];
    }

    private function getTotalReservation(array $filtre): int
    {
        $data = parent::countElement($filtre);
        return $data;
    }

    private function recapStatutReservation(array $filtre): array
    {
        $data = parent::getAll($filtre)->all();
        return array_reduce($data, function ($carry, $item) {
            if ($item->etat_facture == 'valide') {
                $carry['est_valide']++;
            } else {
                $carry['non_valide']++;
            }
            return $carry;
        }, [
            'est_valide' => 0,
            'non_valide' => 0,
        ]);
    }

    private function getDestination(array $filters)
    {

        $data = $this->repository->getReservationParDestination($filters, $this->scope);
        return $data;
    }

    private function recapVoyageClient(array $filtre)
    {
        $data = $this->repository->recapVoyageClient($filtre, $this->scope);
        return $data;
    }
    public function planningReservation(array $filtre = [])
    {
        $reservations = $this->getAll($filtre);
        return $reservations->map(function ($r) {
            return [
                'id' => $r->id,
                'libelle' => $r->numero_reservation,
                'description' => null,
                'date_maintenance' => Carbon::parse($r->date_reservation)->format('d-m-Y'),
                'background' => '#52c41a',
                'text_color' => '#ffffff',
            ];
        })->all();
    }

    public function prepareVoyagesForReservation(Reservation $reservation)
    {
        $reservation->load('voyages.vehicule');
        $existingVoyages = $reservation->voyages;
        $numberVoyagesToCreate = $reservation->nbr_vehicule - $existingVoyages->count();
        // Marquer les voyages existant comme pas fictifs
        $voyages = $existingVoyages->map(function ($voyage) {
            $voyage->is_fictif = false;
            return $voyage;
        });
        // Générer les voyages fictifs si besoin
        if ($numberVoyagesToCreate > 0) {
            $lastVoyageCount = $this->voyageService->getNumeroVoyage() - 1;
            for ($i = 0; $i < $numberVoyagesToCreate; $i++) {
                $lastVoyageCount++;
                $numeroData = $this->generateNumeroVoyageUnique($lastVoyageCount);
                $voyages->push((object) [
                    'id'            => null,
                    'numero_voyage' => $numeroData['numero_voyage'],
                    'is_fictif'     => true,
                ]);
            }
        }
        return $voyages;
    }
    private function generateNumeroVoyageUnique(&$lastVoyageCount)
    {
        do {
            $numeroData = $this->voyageService->generateNumeroVoyage($lastVoyageCount);
            $lastVoyageCount++;
        } while ($this->voyage->where('numero_voyage', $numeroData['numero_voyage'])->exists());
        return $numeroData;
    }

}
