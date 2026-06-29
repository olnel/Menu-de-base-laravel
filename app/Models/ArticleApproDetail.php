<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleApproDetail extends Basemodel
{
    use HasFactory;

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
    public function article_approvisionnement()
    {
        return $this->belongsTo(ArticleApprovisionnement::class);
    }

    public function pneuSeries()
    {
        return $this->hasMany(PneuSerie::class, 'article_appro_detail_id');
    }

    public function toArray()
    {
        $default = parent::toArray();

        $default['reference'] = $this->article?->reference;
        $default['designation'] = $this->article?->designation;
        $default['nom_famille_article'] = $this->article?->famille?->nom_famille_article;
        $default['type_article'] = $this->article?->type_article;

        return $default;
    }
}
