<?php

namespace App\Repositories;

use App\Models\DevisClientDetails;

class DevisClientDetailsRepository extends BaseRepository
{
    protected $repository;
    public function __construct(DevisClientDetails $devisClientDetails)
    {
        $this->repository = $devisClientDetails;
        parent::__construct($devisClientDetails);
    }

    public function deleteByDevisID(mixed $id)
    {
        return $this->repository->where('devis_client_id', $id)->delete();
    }

}
