<?php

namespace App\Services;

class CalendrierService
{
    protected PlanningCalendarService $planningcalendarservice;
    protected VoyageService $voyageService;
    protected ReservationService $reservationService;
    protected ChauffeurDocumentService $chauffeurDocumentService;
    protected VehiculeDocumentService $vehiculeDocumentService;
    protected RemorqueDocumentService $remorqueDocumentService;
    protected DocumentDynamicService $documentDynamicService;
    public function __construct(
        PlanningCalendarService $planningCalendarService, 
        VoyageService $voyageService, 
        ReservationService $reservationService,
        ChauffeurDocumentService $chauffeurDocumentService,
        VehiculeDocumentService $vehiculeDocumentService,
        RemorqueDocumentService $remorqueDocumentService,
        DocumentDynamicService $documentDynamicService
    ) {
        $this->planningcalendarservice = $planningCalendarService;
        $this->voyageService = $voyageService;
        $this->reservationService = $reservationService;
        $this->chauffeurDocumentService = $chauffeurDocumentService;
        $this->vehiculeDocumentService = $vehiculeDocumentService;
        $this->remorqueDocumentService = $remorqueDocumentService;
        $this->documentDynamicService = $documentDynamicService;
    }

    // Your service methods go here
    public function getData($filtre)
    {
        $voyages = $this->getVoyage($filtre);
        $reservations = $this->getReservation($filtre);
        $expiredDocchauffeur = $this->getExpiredDateDocumentChauffuer($filtre);
        $expiredDocVehicule = $this->getExpiredDateDocumentVehicule($filtre);
        $expiredDocRemorque = $this->getExpiredDateDocumentRemorque($filtre);
        $expiredDocDynamic = $this->documentDynamicService->planningDocumentDynamicExpiration($filtre);
        
        $allEvents = array_merge($voyages, $reservations, $expiredDocchauffeur, $expiredDocVehicule, $expiredDocRemorque, $expiredDocDynamic);
        
        return $this->formatData($allEvents);
    }

    public function getReservation($filtre)
    {
        return $this->reservationService->planningReservation($filtre);
    }
    public function getVoyage($filtre)
    {
        return $this->voyageService->planningVoyage($filtre);
    }
    public function getExpiredDateDocumentChauffuer($filtre){
        return $this->chauffeurDocumentService->planningDocumentChauffeurExpiration($filtre);
    }
    public function getExpiredDateDocumentVehicule($filtre){
        return $this->vehiculeDocumentService->planningDocumentVehiculeExpiration($filtre);
    }
    public function getExpiredDateDocumentRemorque($filtre){
        return $this->remorqueDocumentService->planningDocumentRemorqueExpiration($filtre);
    }


    private function formatData($data)
    {
        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'title' => $item['libelle'] ?? ($item['title'] ?? ''),
                'description' => $item['description'] ?? null,
                'date' => $item['date_maintenance'] ?? ($item['date'] ?? date('Y-m-d')),
                'backgroundColor' => $item['background'] ?? ($item['backgroundColor'] ?? '#1890ff'),
                'textColor' => $item['text_color'] ?? ($item['textColor'] ?? '#ffffff'),
                'type' => $item['type'] ?? null,
                'entity_type' => $item['entity_type'] ?? null,
                'nom_document' => $item['nom_document'] ?? null,
                'entity_name' => $item['entity_name'] ?? null
            ];
        }, $data);
    }
}
