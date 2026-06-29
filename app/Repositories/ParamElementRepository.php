<?php

namespace App\Repositories;

use App\Models\ParamElement;

class ParamElementRepository extends BaseRepository
{
    public function __construct(ParamElement $paramElement)
    {
        parent::__construct($paramElement);
    }

}
