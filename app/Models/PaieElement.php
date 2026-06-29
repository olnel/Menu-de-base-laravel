<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaieElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'paie_id',
        'type',
        'libelle',
        'montant',
    ];

    public function paie()
    {
        return $this->belongsTo(Paie::class);
    }

    protected static function booted()
    {
        static::saved(function ($element) {
            $element->paie->recalculate();
        });

        static::deleted(function ($element) {
            $element->paie->recalculate();
        });
    }
}
