<?php

namespace App\Models;

use App\Traits\HasValueText;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Client extends Basemodel
{
    use HasFactory;
    use HasValueText;
    use LogsActivity;

    public string $logModule = 'client';

    protected $fillable = [
        'numero',
        'nom_client',
        'adresse_client',
        'mail_client',
        'login',
        'mot_de_passe',
        'tel_client',
        'nif_client',
        'stat_client',
        'rcs_client',
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    protected $casts = [
        'mot_de_passe' => 'hashed',
    ];

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where('nom_client', 'LIKE', "%$search%")
                ->orWhere('adresse_client', 'LIKE', "%$search%")
                ->orWhere('numero', 'LIKE', "%$search%")
                ->orWhere('nif_client', 'LIKE', "%$search%")
                ->orWhere('mail_client', 'LIKE', "%$search%")
                ->orWhere('tel_client', 'LIKE', "%$search%")
                ->orWhere('stat_client', 'LIKE', "%$search%")
                ->orWhere('rcs_client', 'LIKE', "%$search%");
        }
        return $query;
    }

    public function devisclients()
    {
        return $this->hasMany(DevisClient::class, 'client_id', 'id');
    }
}
