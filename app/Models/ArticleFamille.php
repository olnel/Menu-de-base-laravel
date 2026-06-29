<?php

namespace App\Models;

use App\Traits\HasValueText;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ArticleFamille extends Basemodel
{
    use HasFactory;
    use HasValueText;

    public function scopeFilter($query, $search = null)
    {
        if(!is_null($search)) {
            $query->where('nom_famille_article', 'LIKE', "%$search%");
        }
        return $query;
    }

    // Define the relationship with the Article model
    public function articles()
    {
        return $this->hasMany(Article::class, 'article_famille_id');
    }

}
