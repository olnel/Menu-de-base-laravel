<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PneuSerie extends Basemodel
{
    use HasFactory;

    protected $casts = [
        'is_existe' => 'boolean',
        'occupe' => 'boolean',
    ];

    protected $appends = ['voyage_count', 'last_voyage_date'];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function approDetail()
    {
        return $this->belongsTo(ArticleApproDetail::class, 'article_appro_detail_id');
    }

    public function appro()
    {
        return $this->belongsTo(ArticleApprovisionnement::class, 'article_approvisionnement_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function remorque()
    {
        return $this->belongsTo(Remorque::class, 'remorque_id');
    }

    public function voyages()
    {
        return $this->belongsToMany(Voyage::class, 'voyage_pneus')
            ->withPivot('is_secours', 'numero_serie', 'position', 'designation', 'etat')
            ->withTimestamps();
    }

    public function getVoyageCountAttribute()
    {
        return $this->voyages()->wherePivot('is_secours', false)->count();
    }

    public function getLastVoyageDateAttribute()
    {
        $lastVoyage = $this->voyages()->orderBy('date_voyage', 'desc')->first();
        return $lastVoyage ? $lastVoyage->date_voyage : null;
    }
}
