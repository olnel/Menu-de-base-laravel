<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoncommandeFournisseurDetail extends Basemodel
{
    use HasFactory;

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function boncommande(): HasMany
    {
        return $this->hasMany(BoncommandeFournisseur::class);
    }
    public function toArray()
    {
        $default = parent::toArray();

        $default['reference'] = $this->article?->reference;
        $default['designation'] = $this->article?->designation;
        $default['nom_famille_article'] = $this->article?->famille?->nom_famille_article;

        return $default;
    }
}
