<?php

namespace App\Http\Controllers;

use App\Models\ReparationVehicule;
use App\Models\Ticket;
use App\Services\PDFService\PDFService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService,
        protected PDFService $PDFService,
    ) {}

    /**
     * Generate tickets for a specific reparation and return the tickets data.
     */
    public function generate(ReparationVehicule $reparation_vehicule)
    {
        $this->ticketService->generateForReparation($reparation_vehicule);

        $tickets = Ticket::where('reparation_vehicule_id', $reparation_vehicule->id)
            ->orderBy('technicien_nom')
            ->get();

        return back()->with([
            'flash' => [
                'tickets' => $tickets,
                'reparation_id' => $reparation_vehicule->id,
            ]
        ]);
    }

    /**
     * Download a single ticket as PDF.
     */
    public function downloadPdf(Ticket $ticket)
    {
        $ticket->load(['reparationVehicule']);

        $this->PDFService
            ->setFilename("Ticket_{$ticket->technicien_nom}_{$ticket->id}")
            ->setPaperSize([0, 0, 226.77, 841.89]) // 80x297mm en points DomPDF
            ->setOrientation('portrait')
            ->setOptions([
                'margin-top' => '5',
                'margin-right' => '5',
                'margin-bottom' => '5',
                'margin-left' => '5',
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $this->PDFService->stream('pdf.ticket', [
            'ticket' => $ticket,
            'entreprise' => \App\Models\InfoSociete::first(),
        ]);
    }

    /**
     * Download all tickets for a reparation as a single PDF.
     */
    public function downloadAllPdf(ReparationVehicule $reparation_vehicule)
    {
        $tickets = Ticket::where('reparation_vehicule_id', $reparation_vehicule->id)
            ->orderBy('technicien_nom')
            ->get();

        if ($tickets->isEmpty()) {
            return back()->with('message.error', 'Aucun ticket à générer pour cette réparation.');
        }

        $this->PDFService
            ->setFilename("Tickets_Reparation_{$reparation_vehicule->id}")
            ->setPaperSize([0, 0, 226.77, 841.89]) // 80x297mm en points DomPDF
            ->setOrientation('portrait')
            ->setOptions([
                'margin-top' => '5',
                'margin-right' => '5',
                'margin-bottom' => '5',
                'margin-left' => '5',
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $this->PDFService->stream('pdf.tickets_list', [
            'tickets' => $tickets,
            'reparation' => $reparation_vehicule,
            'entreprise' => \App\Models\InfoSociete::first(),
        ]);
    }
}
