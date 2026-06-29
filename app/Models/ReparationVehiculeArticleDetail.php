<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReparationVehiculeArticleDetail extends Basemodel
{
    use HasFactory;

    protected $appends = ['stock_disponible'];

    public function reparationVehiculeArticle()
    {
        return $this->belongsTo(ReparationVehiculeArticle::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function articleChanger()
    {
        return $this->belongsTo(Article::class, 'article_changer_id');
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'reparation_vehicule_article_detail_id');
    }

    public function getStockDisponibleAttribute()
    {
        $stock = ArticleMagasin::where('magasin_id', $this->magasin_id)->where('article_id', $this->article_id)->first();
        return $stock ? $stock->stock : 0;
    }
}
