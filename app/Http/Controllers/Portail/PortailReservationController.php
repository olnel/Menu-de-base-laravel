<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortailReservationIndexRequest;
use App\Http\Requests\StorePortailReservationRequest;
use App\Http\Requests\UpdatePortailReservationRequest;
use App\Models\Client;
use App\Models\Reservation;
use App\Services\NotificationService;
use App\Services\ReservationService;
use App\Utils\Portail\ExtractFiltrePortailReservation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortailReservationController extends Controller
{
    public function __construct(
        private ReservationService  $reservationService,
        private NotificationService $notificationService,
    ) {}

    private function sessionKey(): string
    {
        return 'portail_client_id_' . tenant('id');
    }

    private function getClient(): Client
    {
        return Client::findOrFail(session($this->sessionKey()));
    }

    public function index(PortailReservationIndexRequest $request)
    {
        $client  = $this->getClient();
        $filters = ExtractFiltrePortailReservation::extractFilter($request);
        $filters['client_id'] = $client->id;

        $data = $this->reservationService->getAll($filters);

        return Inertia::render('Portail/Reservation/Index', [
            'client'  => $client,
            'data'    => $data,
            'flash'   => session('flash', []),
            'filters' => $filters,
        ]);
    }

    public function generatenumero()
    {
        $output       = $this->reservationService->generateNumeroReservation();
        $lieu_options = $this->reservationService->getAllLieuOptions();

        return back()->with([
            'flash' => ['data' => $output, 'lieu_options' => $lieu_options],
        ]);
    }

    public function show(Reservation $reservation)
    {
        $client = $this->getClient();
        abort_if($reservation->client_id !== $client->id, 403);

        $lieu_options = $this->reservationService->getAllLieuOptions();

        return back()->with([
            'flash' => ['data' => $reservation, 'lieu_options' => $lieu_options],
        ]);
    }

    public function store(StorePortailReservationRequest $request)
    {
        $client    = $this->getClient();
        $validated = $request->validated();

        $validated['nom_client']   = $client->nom_client;
        $validated['etat_facture'] = 'non_valide';
        $validated['by_client']    = true;
        $validated['user_id']      = null;

        $output = $this->reservationService->create($validated);

        if (!$output['error']) {
            $this->notificationService->notifyPortailReservation($output['element']);
        }

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function update(UpdatePortailReservationRequest $request, Reservation $reservation)
    {
        $client = $this->getClient();
        abort_if($reservation->client_id !== $client->id, 403);

        $validated = $request->validated();

        $output = $this->reservationService->update($reservation, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function destroy(Reservation $reservation)
    {
        $client = $this->getClient();
        abort_if($reservation->client_id !== $client->id, 403);

        $this->reservationService->delete($reservation);
        return back()->with('message.success', 'Réservation supprimée avec succès.');
    }
}
