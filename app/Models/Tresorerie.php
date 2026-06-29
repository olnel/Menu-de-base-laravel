<?php

namespace App\Models;

use App\Traits\HasValueText;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Tresorerie extends Basemodel
{
    use HasFactory;
    use HasValueText;
    use LogsActivity;

    public string $logModule = 'tresorerie';
    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)){
            $query->where(function($q) use ($search) {
                $q->where('nom_tresorerie', 'LIKE', "%{$search}%")
                    ->orWhere('commentaire', 'LIKE', "%{$search}%")
                    ->orWhere('type_tresorerie', 'LIKE', "%{$search}%")
                    ->orWhere('bic', 'LIKE', "%{$search}%")
                    ->orWhere('iban', 'LIKE', "%{$search}%")
                    ->orWhere('banque_correspondante', 'LIKE', "%{$search}%")
                    ->orWhere('code_swift', 'LIKE', "%{$search}%")
                    ->orWhere('numero_telephone', 'LIKE', "%{$search}%")
                    ->orWhere('numero_compte', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }
}
