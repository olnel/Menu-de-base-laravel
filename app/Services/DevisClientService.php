<?php

namespace App\Services;

use App\Models\BoncommandeFournisseur;
use App\Models\DevisClient;
use App\Repositories\DevisClientDetailsRepository;
use App\Repositories\DevisClientRepository;
use App\Services\Base\BaseService;
use App\Services\Document\DocumentConfig;
use App\Services\Document\DocumentService;
use App\Services\PDFService\PDFService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DevisClientService extends BaseService
{
    protected $repository;
    const MSG_INSERT_SUCCESS = 'INSERTION TERMINÉE AVEC SUCCÈS';
    const MSG_UPDATE_SUCCESS = 'MODIFICATION TERMINÉE AVEC SUCCÈS';
    const MSG_DELETE_SUCCESS = 'SUPPRESSION TERMINÉE AVEC SUCCÈS';
    const MSG_ERROR_INSERT = 'ERREUR LORS DE L\'INSERTION';
    const MSG_ERROR_UPDATE = 'ERREUR LORS DE LA MODIFICATION';
    const MSG_ERROR_DELETE = 'ERREUR LORS DE LA SUPPRESSION';

    protected ClientService $clientService;
    protected DevisClientDetailsRepository $clientDetailsRepository;

    protected DocumentService $documentService;
    protected array $scope = ['filter' => 'search', 'filterdatestart' => 'start_date', 'filterdateend' => 'end_date', 'filterClient' => 'nom_client'];

    public function __construct(
        DevisClientRepository        $devisClientRepository,
        ClientService                $clientService,
        DevisClientDetailsRepository $devisClientDetailsRepository,
        DocumentService $documentService,
    )
    {
        $this->repository = $devisClientRepository;
        $this->clientService = $clientService;
        $this->clientDetailsRepository = $devisClientDetailsRepository;
        $this->documentService = $documentService;

        parent::__construct($devisClientRepository);
    }

    /**
     * Sauvegarde un devis avec ses détails
     * @param array $validated Données validées
     * @return array
     */
    public function saveDevisWithDetails(array $validated): array
    {
        DB::beginTransaction();

        try {

            if (!isset($validated['details'], $validated['nom_client'])) {
                throw new \InvalidArgumentException('Données requises manquantes');
            }

            $details = $validated['details'];

            // Étape 1 : Création ou mise à jour du client
            $client = $this->saveClient($validated['nom_client']);
            $validated['client_id'] = $client['element']->id;

            // Étape 2 : Enregistrement du devis
            $devis = $this->saveDevis($this->prepareDevisData($validated));

            // Étape 3 : Enregistrement des détails
            if (!empty($details)) {
                $this->saveDetails($devis['element']->id, $details);
            }

            DB::commit();
            return $this->successResponse(self::MSG_INSERT_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur saveDevisWithDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(self::MSG_ERROR_INSERT, $e);
        }
    }

    private function saveClient(string $nom_client): array
    {
        return $this->clientService->updateOrCreate($nom_client);
    }

    private function prepareDevisData(array $validated): array
    {
        return [
            'client_id' => $validated['client_id'],
            'date_devis' => $validated['date_devis'],
            'montant_total' => $validated['montant_total'] ?? 0,
            'validite_devis' => $validated['validite_devis'],
            'condition_delais' => $validated['condition_delais'],
            'condition_paiement' => $validated['condition_paiement'],
            'user_id' => auth()->id(),
        ];
    }

    private function saveDevis(array $preparedData): array
    {
        $numeroDevis = $this->generateNumeroCommande();
        $preparedData['numero_devis'] = $numeroDevis['numero_devis'];
        $preparedData['count_devis'] = $numeroDevis['count_devis'];

        return parent::create($preparedData);
    }

    private function saveDetails(int $devisId, array $details): void
    {
        foreach ($details as $detail) {
            if (!is_array($detail)) {
                continue;
            }
            $detail['devis_client_id'] = $devisId;
            $this->clientDetailsRepository->create($detail);
        }
    }

    public function updateDevisWithDetails(\App\Models\DevisClient $devisclient, mixed $validated)
    {
        DB::beginTransaction();

        try {
            // Étape 1 : Supprimer anciens détails
            $this->clientDetailsRepository->deleteByDevisID($devisclient->id);

            $details = $validated['details'];

            // Étape 2 : Création ou mise à jour du client
            $client = $this->saveClient($validated['nom_client']);
            $validated['client_id'] = $client['element']->id;

            // Étape 3 : Enregistrement du devis
            $devis = $this->UpdateDevis($this->prepareDevisData($validated), $devisclient);

            // Étape 4 : Enregistrement des détails
            if (!empty($details)) {
                $this->saveDetails($devis['element']->id, $details);
            }

            DB::commit();
            return $this->successResponse(self::MSG_UPDATE_SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur updateBonCommandeWithDetails', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->errorResponse(self::MSG_ERROR_UPDATE, $e);
        }
    }

    private function UpdateDevis(array $prepareDevisData, \App\Models\DevisClient $devisclient): array
    {
        return parent::update($devisclient, $prepareDevisData);
    }


    /**
     * Supprime un devis et ses détails
     * @param DevisClient $devisClient
     * @return array
     */
    public function deleteDevisWithDetails(DevisClient $devisClient): array
    {
        DB::beginTransaction();

        try {
            // Suppression des détails associés
            $this->clientDetailsRepository->deleteByDevisId($devisClient->id);
            // Suppression du devis
            parent::delete($devisClient);

            DB::commit();
            return $this->successResponse(self::MSG_DELETE_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse(self::MSG_ERROR_DELETE, $e);
        }
    }

    /**
     * Génère le numéro de commande en fonction du mois courant
     */
    private function getNumeroCommande(): int
    {
        $first_date = Carbon::now()->startOfMonth()->toDateString();
        $last_date = Carbon::now()->endOfMonth()->toDateString();

        $lastCommande = $this->repository->find($first_date, $last_date);

        return $lastCommande ? $lastCommande->count_devis + 1 : 1;
    }

    /**
     * Génère le numéro formaté du bon de commande
     */
    public function generateNumeroCommande(): array
    {
        $count = $this->getNumeroCommande();
        $moisAnnee = Carbon::now()->format('m-Y');
        $numero = sprintf('DEV-%03d/%s', $count, $moisAnnee);

        return [
            'count_devis' => $count,
            'numero_devis' => $numero
        ];
    }

    /**
     * Génère le PDF pour un bon de commande
     * @throws \Exception
     */
    public function generatePdf(DevisClient $devisClient)
    {
        try {

            $devisClient->load(['details', 'client']);

            if (!$devisClient->client) {
                throw new \Exception("Client non trouvé pour ce devis");
            }

            $config = DocumentConfig::forDevis($devisClient);
            $result = $this->documentService->generateAndDownloadPdf($config['pdf']);

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Envoie le bon de commande par email au fournisseur
     */
    public function sendMail(DevisClient $devisClient): array
    {
        $devisClient->load(['details', 'client']);
        $config = DocumentConfig::forDevis($devisClient);
        return $this->documentService->sendEmailWithPdf($config);
    }

}
