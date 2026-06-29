<?php

namespace App\Services;

use App\Models\ReparationVehicule;
use App\Models\ReparationVehiculeArticleDetail;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketService
{
    /**
     * Generate all tickets for a given reparation.
     * Deletes existing tickets and regenerates them from current data.
     */
    public function generateForReparation(ReparationVehicule $reparation): void
    {
        $reparation->load(['articles.details']);

        $newTicketIds = [];

        foreach ($reparation->articles as $article) {
            foreach ($article->details as $detail) {
                // Ticket 1 : Main d'œuvre principale (technicien)
                if ($detail->technicien) {
                    $ticket = $this->createTicket($detail, $reparation, $article, 'principal');
                    $newTicketIds[] = $ticket->id;
                }

                // Ticket 2 : Main d'œuvre "changer" (technicien_changer)
                if ($detail->technicien_changer) {
                    $ticket = $this->createTicket($detail, $reparation, $article, 'changer');
                    $newTicketIds[] = $ticket->id;
                }
            }
        }

        // Delete tickets that no longer exist for this reparation
        Ticket::where('reparation_vehicule_id', $reparation->id)
            ->whereNotIn('id', $newTicketIds)
            ->delete();
    }

    /**
     * Create a single ticket from a detail line.
     */
    protected function createTicket(
        ReparationVehiculeArticleDetail $detail,
        ReparationVehicule $reparation,
        $article,
        string $type
    ): Ticket {
        $isPrincipal = $type === 'principal';

        $data = [
            'reparation_vehicule_id' => $reparation->id,
            'reparation_vehicule_article_id' => $article->id,
            'reparation_vehicule_article_detail_id' => $detail->id,
            'technicien_nom' => $isPrincipal ? $detail->technicien : $detail->technicien_changer,
            'type_main_oeuvre' => $type,
            'tarif_horaire' => $isPrincipal ? $detail->tarifs_horaires : $detail->tarifs_horaires_changer,
            'nbre_heure' => $isPrincipal ? $detail->nbre_heure : $detail->nbre_heure_changer,
            'total' => $isPrincipal ? $detail->total_main_oeuvre : $detail->total_main_oeuvre_changer,
            'designation_article' => $detail->designation_article,
            'reference_article' => $detail->reference_article,
            'designation_article_changer' => $detail->designation_article_changer,
            'reference_article_changer' => $detail->reference_article_changer,
            'immatriculation' => $reparation->immatriculation,
            'numero_remorque' => $article->is_remorque ? $article->numero_remorque : null,
            'date_reparation' => $reparation->date_reparation,
            'observations_reparation' => $reparation->observations,
            'user_id' => Auth::id(),
        ];

        // Try to find existing ticket for this detail + type to update it
        $ticket = Ticket::updateOrCreate(
            [
                'reparation_vehicule_article_detail_id' => $detail->id,
                'type_main_oeuvre' => $type,
            ],
            $data
        );

        return $ticket;
    }

    /**
     * Generate tickets for a single detail line (useful when called during save).
     */
    public function generateForDetail(ReparationVehiculeArticleDetail $detail): void
    {
        $detail->load('reparationVehiculeArticle.reparationVehicule');
        $article = $detail->reparationVehiculeArticle;
        $reparation = $article->reparationVehicule;

        $newIds = [];

        if ($detail->technicien) {
            $t = $this->createTicket($detail, $reparation, $article, 'principal');
            $newIds[] = $t->id;
        }

        if ($detail->technicien_changer) {
            $t = $this->createTicket($detail, $reparation, $article, 'changer');
            $newIds[] = $t->id;
        }

        Ticket::where('reparation_vehicule_article_detail_id', $detail->id)
            ->whereNotIn('id', $newIds)
            ->delete();
    }
}
