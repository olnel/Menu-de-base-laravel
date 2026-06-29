<?php

namespace App\Http\Controllers;


use App\Services\DashboardService;
use App\Services\ReservationService;
use App\Services\VehiculeService;
use App\Services\VoyageService;
use App\Utils\ExtractFiltreDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function __construct(private readonly VehiculeService $vehiculeService, private readonly DashboardService $dashboardService)
    {
    }

    public function index(Request $request)
    {
        return Inertia::render("Accueil/HeroPage/Index", [
            "data" => [],
            "filters" => [
                "search" => $request->search
            ]
        ]);
    }

    public function voyage(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);

        $data = $this->dashboardService->getVoyageReservationByDestination($filtre);

        return Inertia::render("Dashboard/Voyage/Index", array_merge($data, [
            "filters" => [
                "search"     => $request->search,
                "start_date" => $request->input('start_date'),
                "end_date"   => $request->input('end_date'),
            ]
        ]));
    }

    public function vehicule(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);
        $data = $this->dashboardService->dashboardVehicule($filtre);

        return Inertia::render("Dashboard/Vehicule/Index", [
            "data" => $data,
            "filters" => [
                "search"     => $request->search,
                "start_date" => $request->input('start_date'),
                "end_date"   => $request->input('end_date'),
            ],
        ]);
    }

    public function comptablite(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);
        $data = $this->dashboardService->dashboardComptabilite($filtre);
        $factureClient = $this->dashboardService->dashboardFactureClient($filtre);

        $data = array_merge($data, $factureClient);
        return Inertia::render("Dashboard/FacturationComptabilite/Index", array_merge($data, [
            "filters" => [
                "search" => $request->search
            ]
        ]));
    }
    public function carburant(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);
        $data = $this->dashboardService->dahsboardCarburant($filtre);
        return Inertia::render("Dashboard/Carburant/Index", [
            "data" => $data,
            "filters" => []
        ]);
    }

    public function pneu(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);
        $data   = $this->dashboardService->dashboardPneu($filtre);
        return Inertia::render("Dashboard/Pneu/Index", [
            "data"    => $data,
            "filters" => [
                "start_date" => $request->input('start_date'),
                "end_date"   => $request->input('end_date'),
            ],
        ]);
    }

    public function chauffeur(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);
        $data   = $this->dashboardService->dashboardChauffeur($filtre);
        return Inertia::render("Dashboard/Chauffeur/Index", [
            "data"    => $data,
            "filters" => [
                "start_date" => $request->input('start_date'),
                "end_date"   => $request->input('end_date'),
            ],
        ]);
    }
}
