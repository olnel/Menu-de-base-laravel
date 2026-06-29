<?php

namespace App\Models;

use App\Traits\HasValueText;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Fournisseur extends Basemodel
{
    use HasFactory;
    use HasValueText;

    public function scopeFilter($query, $search = null)
    {
        if(!is_null($search)) {
            $query->where('nom_fournisseur', 'LIKE', "%$search%")
                    ->orWhere('adresse_fournisseur',  'LIKE', "%$search%")
                    ->orWhere('nif_fournisseur',  'LIKE', "%$search%")
                    ->orWhere('tel_fournisseur',  'LIKE', "%$search%")
                    ->orWhere('stat_fournisseur',  'LIKE', "%$search%")
                    ->orWhere('rcs_fournisseur',  'LIKE', "%$search%")
                    ->orWhere('mail_fournisseur',  'LIKE', "%$search%");
        }
        return $query;
    }
}
