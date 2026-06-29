<?php

namespace App\Repositories;

use App\Models\PrimeConfig;

class PrimeConfigRepository extends BaseRepository
{
    public function __construct(PrimeConfig $primeConfig)
    {
        parent::__construct($primeConfig);
    }
}
