<?php

namespace App\Services;


use App\constants\Messagenotification;
use App\Models\FactureClient;
use App\Repositories\FactureClientRepository;
use App\Repositories\VoyageRepository;
use App\Services\Base\BaseService;
use App\Services\Document\DocumentConfig;
use App\Services\Document\DocumentService;
use App\Services\NumeroGeneratorService\NumeroGeneratorService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class FactureClientService extends BaseService
{
    protected $repository;
    protected array $scope = [
        'filter' => 'search',
        'filterclient' => 'client_id',
        'filterstatut' => 'statut_facture',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date'
    ];
    public function __construct(FactureClientRepository $factureClientRepository, protected NumeroGeneratorService $numeroGeneratorService,
                                protected readonly ReservationService $reservationService, protected readonly VoyageRepository $voyageRepository,
                                protected readonly DocumentService $documentService)
    {
        $this->repository = $factureClientRepository;
        parent::__construct($factureClientRepository);

        $this->repository->setDefaultOrder('date_facture', 'desc');
    }
    /**
     * Retourne le numéro de facture du mois en cours
     *
     * @return array
     */
    public function generateInvoiceNumber(): array
    {
        return $this->numeroGeneratorService->generate(
            function ($firstDate, $lastDate) {
                $lastCommande = $this->repository->find($firstDate, $lastDate);
                return $lastCommande?->count_facture;
            },
            prefix: 'FACT'
        );
    }

    public function saveFacture(mixed $validated)
    {

        DB::beginTransaction();

        try {
            $data = $this->generateInvoiceNumber();

            $validated['numero_facture'] =  $data['numero'];
            $validated['count_facture'] =  $data['count'];

            $validated['voyage_ids'] = null;

            if (isset($validated['voyages']) && is_array($validated['voyages']) && !empty($validated['voyages'])) {
                $validated['voyage_ids'] = json_encode(($validated['voyages']));
            }
            $validated['user_id'] = auth()->user()->id;
            $validated['montant_reste_a_payer'] = $validated['montant_ttc'];


            $facture_client_result = parent::create($validated);

            if ($facture_client_result['error']) {
                throw new Exception($facture_client_result['message']);
            }

            $this->changeStatutVoyage($validated, $facture_client_result);

            DB::commit();

            return $this->successResponse(Messagenotification::MSG_INSERT_SUCCESS);
        } catch (Exception $e) {

            DB::rollBack();
            Log::error(Messagenotification::MSG_ERROR_INSERT . ' : ' . $e->getMessage(), ['exception' => $e->getMessage()]);
            return $this->errorResponse(Messagenotification::MSG_ERROR_INSERT, $e);
        }
    }

    public function updateFacture(FactureClient $factureClient,mixed $validated)
    {
        if ($factureClient->montant_payer > 0) {
            return $this->errorResponse('La facture ne peut pas être modifiée car elle a déjà été partiellement payée.', new Exception('La facture a déjà des paiements.'));
        }

        DB::beginTransaction();
        try {
            $validated['user_id'] = auth()->user()->id;
            $validated['montant_reste_a_payer'] = $validated['montant_ttc'];

            parent::update($factureClient,$validated);

            DB::commit();
            return $this->successResponse(Messagenotification::MSG_UPDATE_SUCCESS);
        } catch (Exception $e) {

            DB::rollBack();
            Log::error(Messagenotification::MSG_ERROR_INSERT . ' : ' . $e->getMessage(), ['exception' => $e->getMessage()]);
            return $this->errorResponse(Messagenotification::MSG_ERROR_UPDATE, $e);
        }
    }

    private function changeStatutVoyage($data, $facture_client_result): void
    {

        $ids_voyage = json_decode($data['voyage_ids'], true);
        foreach ($ids_voyage as $voyage_id) {
            $voyage = $this->voyageRepository->findElement(['id' => $voyage_id]);
            $voyage->facture_client_id = $facture_client_result['element']->id;
            $voyage->save();
        }
    }

    public function generatePdf(FactureClient $factureclient)
    {
        $factureclient->load([ 'client']);


        $config = DocumentConfig::forFactureClient($factureclient);
        return $this->documentService->generateAndDownloadPdf($config['pdf']);
    }

    public function sendMail(FactureClient $factureclient)
    {
        $factureclient->load(['client']);
        $config = DocumentConfig::forFactureClient($factureclient);
        try {
            $this->documentService->sendEmailWithPdf($config);
            $factureclient->statut_facture = 'envoyée';
            $factureclient->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getHtmlForPrint(FactureClient $factureclient)
    {
        $factureclient->load(['client']);
        $config = DocumentConfig::forFactureClient($factureclient);
        return $this->documentService->generateHtml($config['pdf']);
    }


    public function dashboard(array $filtre): array
    {
        return $this->repository->recapFactureCliffent($filtre, $this->scope);
    }

}
