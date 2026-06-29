<?php

namespace App\Repositories;

use App\Models\VehiculePhoto;

class VehiculePhotoRepository extends BaseRepository
{
    public function __construct(VehiculePhoto $vehiculePhoto)
    {
        parent::__construct($vehiculePhoto);
    }

    public function getElementsByVehicule(int $vehiculeId)
    {
        return $this->model->where('vehicule_id', $vehiculeId)
            ->orderBy('date_prise_photo', 'desc')
            ->get(['id', 'vehicule_id', 'liste_image', 'date_prise_photo','etat_vehicule','type_element'])
            ->map(function($photo) {
                $photo->liste_image = json_decode($photo->liste_image, true) ?? [];
                return $photo;
            });
    }
}
