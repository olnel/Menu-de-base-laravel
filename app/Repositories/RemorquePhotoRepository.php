<?php

namespace App\Repositories;

use App\Models\RemorquePhoto;
use App\Models\VehiculePhoto;

class RemorquePhotoRepository extends BaseRepository
{
    public function __construct(RemorquePhoto $remorquePhoto)
    {
        parent::__construct($remorquePhoto);
    }

    public function getElementsByVehicule(int $vehiculeId)
    {
        return $this->model->where('remorque_id', $vehiculeId)
            ->orderBy('date_prise_photo', 'desc')
            ->get(['id', 'remorque_id', 'liste_image', 'date_prise_photo','etat_vehicule','type_element'])
            ->map(function($photo) {
                $photo->liste_image = json_decode($photo->liste_image, true) ?? [];
                return $photo;
            });
    }
}
