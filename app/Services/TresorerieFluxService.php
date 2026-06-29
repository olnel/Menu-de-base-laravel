<?php

namespace App\Services;

use App\Models\Tresorerie;
use App\Repositories\TresorerieFluxRepository;
use App\Repositories\TresorerieRepository;
use App\Services\Base\BaseService;
use Exception;

class TresorerieFluxService extends BaseService
{
    protected $repository;
    protected TresorerieRepository $tresorerierepository;
    protected array $scope = [
        'filter' => 'search',
        'tresorerie' => 'tresorerie_id',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date'
    ];

    public function __construct(
        TresorerieFluxRepository $repository,
        TresorerieRepository $tresorerieRepository
    ) {
        $this->repository = $repository;
        $this->tresorerierepository = $tresorerieRepository;
        parent::__construct($repository);
    }

    /**
     * Traite un mouvement et crée les flux correspondants
     */
    public function processMouvement(array $data): void
    {
        switch ($data['type_mvt']) {
            case 'ENTREE':
            case 'SORTIE':
                $this->processSimpleMouvement($data);
                break;

            case 'TRANSFERT':
                $this->processTransfert($data);
                break;

            case 'AJUSTEMENT':
                $this->processAjustement($data);
                break;

            default:
                throw new Exception('Type de mouvement non supporté');
        }
    }

    /**
     * Annule un mouvement et ses flux
     */
    public function cancelMouvement(array $data): void
    {
        switch ($data['type_mvt']) {
            case 'ENTREE':
                $this->cancelSimpleMouvement($data, 'SORTIE');
                break;

            case 'SORTIE':
                $this->cancelSimpleMouvement($data, 'ENTREE');
                break;

            case 'TRANSFERT':
                $this->cancelTransfert($data);
                break;

            case 'AJUSTEMENT':
                $this->cancelAjustement($data);
                break;
        }
    }

    /**
     * Traite les mouvements simples (ENTREE/SORTIE)
     */
    private function processSimpleMouvement(array $data): void
    {
        $tresorerie = $this->getTresorerie($data['tresorerie_id']);

        $fluxData = [
            'date_mvt' => $data['date_mvt'],
            'libelle_mvt' => $data['libelle_mvt'],
            'tresorerie_id' => $tresorerie->id,
            'type_mvt' => $data['type_mvt'],
            'montant' => $data['montant'],
            'operation_mvt' => $data['operation_mvt'] ?? null,
            'mode_paiement' => $data['mode_paiement'],
            'commentaire' => $data['commentaire'],
            'user_id' => $data['user_id'],
            'solde_avant' => $tresorerie->solde,
            'solde_final' => $this->calculateNewSolde($tresorerie, $data),
            'poste_depense' => $data['poste_depense'] ?? null
        ];

        $this->createFluxAndUpdateSolde($fluxData, $tresorerie);
    }

    /**
     * Traite un transfert entre trésoreries
     */
    private function processTransfert(array $data): void
    {
        $source = $this->getTresorerie($data['tresorerie_id']);
        $target = $this->getTresorerie($data['tresorerie_id_cible']);

        // Flux sortant
        $outData = [
            'date_mvt' => $data['date_mvt'],
            'libelle_mvt' => "Transfert vers {$target->nom_tresorerie}",
            'tresorerie_id' => $source->id,
            'type_mvt' => 'SORTIE',
            'montant' => $data['montant'],
            'operation_mvt' => "Transfert à {$target->nom_tresorerie}",
            'mode_paiement' => $data['mode_paiement'],
            'commentaire' => $data['commentaire'],
            'user_id' => $data['user_id'],
            'solde_avant' => $source->solde,
            'solde_final' => $source->solde - $data['montant'],
        ];

        // Flux entrant
        $inData = [
            'date_mvt' => $data['date_mvt'],
            'libelle_mvt' => "Transfert de {$source->nom_tresorerie}",
            'tresorerie_id' => $target->id,
            'type_mvt' => 'ENTREE',
            'montant' => $data['montant'],
            'operation_mvt' => "Transfert de {$source->nom_tresorerie}",
            'mode_paiement' => $data['mode_paiement'],
            'commentaire' => $data['commentaire'],
            'user_id' => $data['user_id'],
            'solde_avant' => $target->solde,
            'solde_final' => $target->solde + $data['montant'],
        ];

        $this->createFluxAndUpdateSolde($outData, $source);
        $this->createFluxAndUpdateSolde($inData, $target);
    }

    /**
     * Traite un ajustement de solde
     */
    private function processAjustement(array $data): void
    {
        $tresorerie = $this->getTresorerie($data['tresorerie_id']);
        $difference = $data['montant'] - $tresorerie->solde;

        if ($difference == 0)
            return;

        $type = $difference > 0 ? 'ENTREE' : 'SORTIE';
        $montant = abs($difference);

        $fluxData = [
            'date_mvt' => $data['date_mvt'],
            'libelle_mvt' => $data['libelle_mvt'],
            'tresorerie_id' => $tresorerie->id,
            'type_mvt' => $type,
            'montant' => $montant,
            'operation_mvt' => "Ajustement vers {$data['montant']}",
            'mode_paiement' => $data['mode_paiement'],
            'commentaire' => $data['commentaire'],
            'user_id' => $data['user_id'],
            'solde_avant' => $tresorerie->solde,
            'solde_final' => $data['montant'],
        ];

        $this->createFluxAndUpdateSolde($fluxData, $tresorerie);
    }

    /**
     * Annule un mouvement simple
     */
    private function cancelSimpleMouvement(array $data, string $reverseType): void
    {
        $tresorerie = $this->getTresorerie($data['tresorerie_id']);

        $fluxData = [
            'date_mvt' => now(),
            'libelle_mvt' => "Annulation {$data['libelle_mvt']}",
            'tresorerie_id' => $tresorerie->id,
            'type_mvt' => $reverseType,
            'montant' => $data['montant'],
            'operation_mvt' => "Annulation mouvement",
            'mode_paiement' => 'Annulation',
            'commentaire' => "Annulation: {$data['commentaire']}",
            'user_id' => $data['user_id'],
            'solde_avant' => $tresorerie->solde,
            'solde_final' => $reverseType === 'ENTREE'
                ? $tresorerie->solde + $data['montant']
                : $tresorerie->solde - $data['montant'],

        ];

        $this->createFluxAndUpdateSolde($fluxData, $tresorerie);
    }

    /**
     * Annule un transfert
     */
    private function cancelTransfert(array $data): void
    {
        $source = $this->getTresorerie($data['tresorerie_id']);
        $target = $this->getTresorerie($data['tresorerie_id_cible']);

        // Annulation flux source (entrée au lieu de sortie)
        $sourceData = [
            'date_mvt' => now(),
            'libelle_mvt' => "Annulation transfert",
            'tresorerie_id' => $source->id,
            'type_mvt' => 'ENTREE',
            'montant' => $data['montant'],
            'operation_mvt' => "Annulation transfert",
            'mode_paiement' => 'Annulation',
            'commentaire' => "Annulation transfert vers {$target->nom_tresorerie}",
            'user_id' => $data['user_id'],
            'solde_avant' => $source->solde,
            'solde_final' => $source->solde + $data['montant'],
        ];

        // Annulation flux cible (sortie au lieu d'entrée)
        $targetData = [
            'date_mvt' => now(),
            'libelle_mvt' => "Annulation transfert",
            'tresorerie_id' => $target->id,
            'type_mvt' => 'SORTIE',
            'montant' => $data['montant'],
            'operation_mvt' => "Annulation transfert",
            'mode_paiement' => 'Annulation',
            'commentaire' => "Annulation transfert de {$source->nom_tresorerie}",
            'user_id' => $data['user_id'],
            'solde_avant' => $target->solde,
            'solde_final' => $target->solde - $data['montant'],
        ];

        $this->createFluxAndUpdateSolde($sourceData, $source);
        $this->createFluxAndUpdateSolde($targetData, $target);
    }

    /**
     * Annule un ajustement
     */
    private function cancelAjustement(array $data): void
    {
        $tresorerie = $this->getTresorerie($data['tresorerie_id']);

        $fluxData = [
            'date_mvt' => now(),
            'libelle_mvt' => "Annulation ajustement",
            'tresorerie_id' => $tresorerie->id,
            'type_mvt' => 'AJUSTEMENT',
            'montant' => abs($data['solde_avant'] - $tresorerie->solde),
            'operation_mvt' => "Rétablissement solde initial",
            'mode_paiement' => 'Annulation',
            'commentaire' => "Annulation ajustement du " . $data['date_mvt'],
            'user_id' => $data['user_id'],
            'solde_avant' => $tresorerie->solde,
            'solde_final' => $data['solde_avant'],
        ];

        $this->createFluxAndUpdateSolde($fluxData, $tresorerie);
    }

    /**
     * Récupère une trésorerie avec vérification
     */
    private function getTresorerie(int $tresorerie_id): Tresorerie
    {
        $tresorerie = $this->tresorerierepository->findElement(['id' => $tresorerie_id]);
        if (!$tresorerie) {
            throw new Exception("Trésorerie introuvable");
        }
        return $tresorerie;
    }

    /**
     * Calcule le nouveau solde
     */
    private function calculateNewSolde(Tresorerie $tresorerie, array $data): float
    {
        return $data['type_mvt'] === 'ENTREE'
            ? $tresorerie->solde + $data['montant']
            : $tresorerie->solde - $data['montant'];
    }

    /**
     * Crée un flux et met à jour le solde
     */
    private function createFluxAndUpdateSolde(array $fluxData, Tresorerie $tresorerie): void
    {
        // Création du flux
        parent::create($fluxData);

        // Mise à jour du solde
        $this->tresorerierepository->update($tresorerie, [
            'solde' => $fluxData['solde_final']
        ]);
    }

    public function dasboard(array $filtre): array
    {
        return [
            'totalGeneral' => $this->getTotalGeneralSolde($filtre),
        ];
    }

    private function getTotalGeneralSolde(array $filtre)
    {
        return $this->repository->queryTotal($filtre, $this->scope);
    }
}
