<?php

namespace App\Services;

use App\Mail\BonCommandeFournisseurMail;
use App\Models\BoncommandeFournisseur;
use App\Repositories\BonCommandeRepository;
use App\Services\Base\BaseService;
use App\Services\Document\DocumentConfig;
use App\Services\Document\DocumentService;
use App\Services\PDFService\PDFService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BoncommandeService extends BaseService
{
    protected $repository;
    private FournisseurService $fournisseurService;
    private BoncommandeDetailService $detailService;
    private DocumentService $documentService;
    private PDFService $PDFService;

    const MSG_INSERT_SUCCESS = 'INSERTION TERMINÉE AVEC SUCCÈS';
    const MSG_UPDATE_SUCCESS = 'MODIFICATION TERMINÉE AVEC SUCCÈS';
    const MSG_DELETE_SUCCESS = 'SUPPRESSION TERMINÉE AVEC SUCCÈS';
    const MSG_ERROR_INSERT = 'ERREUR LORS DE L\'INSERTION';
    const MSG_ERROR_UPDATE = 'ERREUR LORS DE LA MODIFICATION';
    const MSG_ERROR_DELETE = 'ERREUR LORS DE LA SUPPRESSION';

    protected array $scope = ['filter' => 'search', 'filterdatestart' => 'start_date', 'filterdateend' => 'end_date', 'filterFournisseur' => 'nom_fournisseur', 'statut' => 'statut'];

    public function __construct(
        BonCommandeRepository $bonCommandeRepository,
        FournisseurService $fournisseurService,
        BoncommandeDetailService $detailService,
        PDFService $PDFService,
        DocumentService $documentService
    ) {
        $this->repository = $bonCommandeRepository;
        $this->fournisseurService = $fournisseurService;
        $this->detailService = $detailService;
        $this->PDFService = $PDFService;
        $this->documentService = $documentService;
    }

    /**
     * Génère le numéro de commande en fonction du mois courant
     */
    private function getNumeroCommande(): int
    {
        $first_date = Carbon::now()->startOfMonth()->toDateString();
        $last_date = Carbon::now()->endOfMonth()->toDateString();

        $lastCommande = $this->repository->find($first_date, $last_date);

        return $lastCommande ? $lastCommande->count_numero_commande + 1 : 1;
    }

    /**
     * Génère le numéro formaté du bon de commande
     */
    public function generateNumeroCommande(): array
    {
        $count = $this->getNumeroCommande();
        $moisAnnee = Carbon::now()->format('m-Y');
        $numero = sprintf('BCF-%03d/%s', $count, $moisAnnee);

        return [
            'count_numero_commande' => $count,
            'numero_bon_commande' => $numero
        ];
    }

    /**
     * Enregistre une commande avec ses détails
     */
    public function saveBonCommandeWithDetails(array $validated): array
    {
        DB::beginTransaction();

        try {
            $details = $validated['details'] ?? [];
            $dateHeure = now();

            // Étape 1 : Création ou mise à jour du fournisseur
            $fournisseur = $this->saveFournisseur($validated['nom_fournisseur']);
            $validated['fournisseur_id'] = $fournisseur['element']->id;

            // Étape 2 : Génération des informations du bon
            $numero = $this->generateNumeroCommande();
            $preparedData = $this->prepareBonCommandeData($validated, $dateHeure, $numero);

            // Étape 3 : Enregistrement du bon
            $bon_commande = $this->saveBonCommande($preparedData);

            // Étape 4 : Enregistrement des détails
            if (!empty($details)) {
                $this->saveDetails($bon_commande['element']->id, $details);
            }

            DB::commit();
            return $this->successResponse(self::MSG_INSERT_SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur saveBonCommandeWithDetails', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->errorResponse(self::MSG_ERROR_INSERT, $e);
        }
    }

    /**
     * Prépare les données du bon de commande
     */
    private function prepareBonCommandeData(array $validated, $dateHeure, array $numero): array
    {
        return [
            ...$validated,
            'date_heure_enregistrement' => $dateHeure,
            'numero_bon_commande' => $numero['numero_bon_commande'],
            'count_numero_commande' => $numero['count_numero_commande'],
            'user_id' => Auth::id(),
        ];
    }

    private function saveFournisseur(string $nomFournisseur): array
    {
        return $this->fournisseurService->updateOrCreateStock($nomFournisseur);
    }

    private function saveBonCommande(array $data): array
    {
        return parent::create($data);
    }

    private function saveDetails(int $bonCommandeId, array $details): void
    {
        foreach ($details as $detail) {
            $detail['boncommande_fournisseur_id'] = $bonCommandeId;
            $this->detailService->create($detail);
        }
    }

    /**
     * Modification d’un bon de commande
     */
    public function updateBonCommandeWithDetails(BoncommandeFournisseur $bonCommande, array $validated): array
    {
        DB::beginTransaction();

        try {
            $details = $validated['details'] ?? [];
            $dateHeure = now();

            // Étape 1 : Supprimer anciens détails
            $this->detailService->deleteByBonCommande($bonCommande->id);

            // Étape 2 : Mise à jour du fournisseur
            $fournisseur = $this->saveFournisseur($validated['nom_fournisseur']);
            $validated['fournisseur_id'] = $fournisseur['element']->id;

            // Étape 3 : Mise à jour du bon
            $validated['date_heure_enregistrement'] = $dateHeure;
            $validated['user_id'] = Auth::id();
            unset($validated['details'], $validated['nom_fournisseur']);

            $bonCommande->update($validated);

            // Étape 4 : Réinsertion des détails
            if (!empty($details)) {
                $this->saveDetails($bonCommande->id, $details);
            }

            DB::commit();
            return $this->successResponse(self::MSG_UPDATE_SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur updateBonCommandeWithDetails', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->errorResponse(self::MSG_ERROR_UPDATE, $e);
        }
    }

    /**
     * Suppression d’un bon de commande et ses détails
     */
    public function deleteBonCommande(BoncommandeFournisseur $model): array
    {
        DB::beginTransaction();

        try {
            $this->detailService->deleteByBonCommande($model->id);
            parent::delete($model);

            DB::commit();
            return $this->successResponse(self::MSG_DELETE_SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur deleteBonCommande', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->errorResponse(self::MSG_ERROR_DELETE, $e);
        }
    }

    /**
     * Génère le PDF pour un bon de commande
     * @throws \Exception
     */
    public function generateBonCommandePdf(BoncommandeFournisseur $bonCommande)
    {
        $bonCommande->load(['details.article', 'fournisseur']);

        if (!$bonCommande->fournisseur) {
            throw new \Exception("Fournisseur non trouvé pour ce bon de commande");
        }

        $config = DocumentConfig::forBonCommande($bonCommande);
        return $this->documentService->generateAndDownloadPdf($config['pdf']);
    }

    /**
     * Envoie le bon de commande par email au fournisseur
     */
    public function sendMail(BoncommandeFournisseur $bonCommande)
    {
        $bonCommande->load('fournisseur');
        $config = DocumentConfig::forBonCommande($bonCommande);
        $this->documentService->sendEmailWithPdf($config);
    }
}
