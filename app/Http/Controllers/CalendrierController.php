<?php

namespace App\Http\Controllers;


use App\Services\CalendrierService;
use App\Utils\ExtractFiltreCalendrier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendrierController extends Controller
{
    private $service;
    public function __construct(CalendrierService $calendrierService)
    {
        $this->service = $calendrierService;
    }

    public function index(Request $request)
    {
        $filtre = ExtractFiltreCalendrier::extractFilter($request);


        $out_put = $this->service->getData($filtre);

        return Inertia::render("Dashboard/Calendrier/Index", [
            "data" => $out_put,
            "filters" => [

            ],
            "flash" => session('flash', [])
        ]);
    }
}
