<?php

namespace App\Services;

use App\Models\Voyage;
use App\Models\VoyagePneu;
use App\Repositories\VehiculeElementRepository;
use App\Repositories\RemorqueElementRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class VoyagePneuService
{
    public function __construct(
        protected readonly VehiculeElementRepository $vehiculeElementRepository,
        protected readonly RemorqueElementRepository $remorqueElementRepository
    ) {}

    /**
     * Synchronise les pneus d'un voyage (remplace la liste existante).
     */
    public function syncPneus(Voyage $voyage, array $pneus): array
    {
        DB::beginTransaction();
        try {
            // Hard delete : table de synchronisation sans soft-delete
            DB::table('voyage_pneus')->where('voyage_id', $voyage->id)->delete();

            foreach ($pneus as $item) {
                VoyagePneu::create([
                    'voyage_id'     => $voyage->id,
                    'pneu_serie_id' => $item['pneu_serie_id'] ?? null,
                    'numero_serie'  => $item['numero_serie']  ?? null,
                    'position'      => $item['position']      ?? null,
                    'designation'   => $item['designation']   ?? null,
                    'etat'          => $item['etat']          ?? null,
                    'is_secours'    => (bool) ($item['is_secours'] ?? false),
                ]);
            }

            DB::commit();
            return ['error' => false, 'message' => 'Pneus du voyage enregistrés avec succès'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['error' => true, 'message' => 'Erreur lors de l\'enregistrement des pneus : ' . $e->getMessage()];
        }
    }

    /**
     * Retourne les pneus (avec is_secours) pour un voyage donné.
     * Si aucun enregistrement, pré-remplit depuis les éléments du véhicule/remorque.
     */
    public function getPneusForVoyage(Voyage $voyage): array
    {
        $existing = DB::table('voyage_pneus')->where('voyage_id', $voyage->id)->get();

        if ($existing->isNotEmpty()) {
            return $existing->map(fn($vp) => [
                'id'            => $vp->id,
                'pneu_serie_id' => $vp->pneu_serie_id,
                'numero_serie'  => $vp->numero_serie,
                'position'      => $vp->position,
                'designation'   => $vp->designation,
                'etat'          => $vp->etat,
                'is_secours'    => (bool) $vp->is_secours,
            ])->toArray();
        }

        // Pré-remplissage depuis véhicule + remorque
        $result = [];

        if ($voyage->vehicule_id) {
            $elements = $this->vehiculeElementRepository->getElementsByVehicule($voyage->vehicule_id);
            foreach ($elements->where('is_pneu', true) as $el) {
                $result[] = $this->formatElement($el, $el->pneuSerie);
            }
        }

        if ($voyage->remorque_id) {
            $elements = $this->remorqueElementRepository->getElementsByVehicule($voyage->remorque_id);
            foreach ($elements->where('is_pneu', true) as $el) {
                $result[] = $this->formatElement($el, $el->pneuSerie);
            }
        }

        return $result;
    }

    private function formatElement($element, $pneuSerie): array
    {
        $pneuSerie?->loadMissing('article');

        return [
            'id'            => null,
            'pneu_serie_id' => $pneuSerie?->id,
            'numero_serie'  => $element->numero_serie,
            'position'      => $element->emplacement,
            'designation'   => $pneuSerie?->article?->libelle ?? $element->libelle,
            'etat'          => $pneuSerie?->etat ?? $element->etat_piece,
            'is_secours'    => false,
        ];
    }
}
