<?php

namespace App\Http\Controllers;

use App\Utils\ExtractFiltreDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
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

        return Inertia::render("Dashboard/Voyage/Index", [
            "filters" => [
                "search"     => $request->search,
                "start_date" => $request->input('start_date'),
                "end_date"   => $request->input('end_date'),
            ]
        ]);
    }

    public function vehicule(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);

        return Inertia::render("Dashboard/Vehicule/Index", [
            "data" => [],
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
        return Inertia::render("Dashboard/FacturationComptabilite/Index", [
            "filters" => [
                "search" => $request->search
            ]
        ]);
    }
    public function carburant(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);
        return Inertia::render("Dashboard/Carburant/Index", [
            "data" => [],
            "filters" => []
        ]);
    }

    public function pneu(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);
        return Inertia::render("Dashboard/Pneu/Index", [
            "data"    => [],
            "filters" => [
                "start_date" => $request->input('start_date'),
                "end_date"   => $request->input('end_date'),
            ],
        ]);
    }

    public function chauffeur(Request $request)
    {
        $filtre = ExtractFiltreDashboard::extractFilter($request);
        return Inertia::render("Dashboard/Chauffeur/Index", [
            "data"    => [],
            "filters" => [
                "start_date" => $request->input('start_date'),
                "end_date"   => $request->input('end_date'),
            ],
        ]);
    }
}
