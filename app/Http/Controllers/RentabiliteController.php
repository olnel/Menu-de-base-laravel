<?php

namespace App\Http\Controllers;

use App\Services\RentabiliteService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RentabiliteController extends Controller
{
    public function __construct(
        private readonly RentabiliteService $rentabiliteService,
    ) {}

    public function index(Request $request)
    {
        $filters = [
            "search" => $request->input("search"),
            "start_date" => $request->input("start_date"),
            "end_date" => $request->input("end_date"),
            "per_page" => $request->input("per_page", 10),
            "page" => $request->input("page", 1),
        ];

        $paginatedData = $this->rentabiliteService->getPaginatedProfitability(
            $filters,
        );
        $summary = $this->rentabiliteService->getGlobalSummary($filters);

        return Inertia::render("Dashboard/Rentabilite/Index", [
            "data" => $paginatedData->items(),
            "pagination" => [
                "current_page" => $paginatedData->currentPage(),
                "per_page" => $paginatedData->perPage(),
                "total" => $paginatedData->total(),
            ],
            "summary" => $summary,
            "filters" => $filters,
        ]);
    }

    public function export(
        Request $request,
        \App\Services\PDFService\PDFService $pdfService,
    ) {
        $filters = [
            "search" => $request->input("search"),
            "start_date" => $request->input("start_date"),
            "end_date" => $request->input("end_date"),
        ];

        $data = $this->rentabiliteService->getAllProfitability($filters);
        $summary = $this->rentabiliteService->getGlobalSummary($filters);

        $exportType = $request->input("type_export", "xlsx"); // 'xlsx' or 'pdf'
        if ($exportType === "excel") {
            $exportType = "xlsx";
        }

        if ($exportType === "pdf") {
            $entreprise = \App\Models\InfoSociete::first();

            // Format dates for subtitle
            $periode = "Toutes dates";
            if ($filters["start_date"] && $filters["end_date"]) {
                $periode =
                    "Du " .
                    \Carbon\Carbon::parse($filters["start_date"])->format(
                        "d/m/Y",
                    ) .
                    " Au " .
                    \Carbon\Carbon::parse($filters["end_date"])->format(
                        "d/m/Y",
                    );
            } elseif ($filters["start_date"]) {
                $periode =
                    "A partir du " .
                    \Carbon\Carbon::parse($filters["start_date"])->format(
                        "d/m/Y",
                    );
            } elseif ($filters["end_date"]) {
                $periode =
                    'Jusqu\'au ' .
                    \Carbon\Carbon::parse($filters["end_date"])->format(
                        "d/m/Y",
                    );
            }

            $title = "Rapport_Rentabilite_Actifs_" . date("Ymd_His");

            return $pdfService
                ->setFilename($title . ".pdf")
                ->setOrientation("landscape") // Landscape orientation is better for large data tables
                ->setPaperSize("A4")
                ->stream("pdf.rentabilite", [
                    "entreprise" => $entreprise,
                    "periode" => $periode,
                    "summary" => $summary,
                    "assets" => $data,
                ]);
        } else {
            // Excel Export
            $exportable = (object) [
                "label" => "Actif",
                "type" => "Type",
                "valeur_initial" => "Investissement",
                "revenus" => "Revenus",
                "nb_voyages" => "Nombre Voyages",
                "depense_maintenance" => "Maintenance",
                "charges_route" => "Charges Route",
                "cout_social" => "Coût Social",
                "marge_nette" => "Marge Nette",
                "roi" => "ROI (%)",
            ];

            // Format data values for export
            $formattedData = $data->map(function ($item) {
                return [
                    "label" => $item["label"] . " (" . $item["sub_label"] . ")",
                    "type" => ucfirst($item["type"]),
                    "valeur_initial" => $item["valeur_initial"],
                    "revenus" => $item["revenus"],
                    "nb_voyages" => $item["nb_voyages"],
                    "depense_maintenance" => $item["depense_maintenance"],
                    "charges_route" => $item["charges_route"],
                    "cout_social" => $item["cout_social"],
                    "marge_nette" => $item["marge_nette"],
                    "roi" => $item["roi"] . "%",
                ];
            });

            $title = "Rentabilite_Actifs_" . date("Ymd_His");

            $exportClass = new \App\Exports\ExportDataexcel(
                $exportable,
                $formattedData,
                $title,
                "xlsx",
            );
            return $exportClass->download();
        }
    }
}
