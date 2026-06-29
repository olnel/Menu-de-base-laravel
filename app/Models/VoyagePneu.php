<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoyagePneu extends Model
{
    use HasFactory;

    protected $table  = 'voyage_pneus';
    protected $guarded = ['id'];

    public function voyage()
    {
        return $this->belongsTo(Voyage::class);
    }

    public function pneuSerie()
    {
        return $this->belongsTo(PneuSerie::class);
    }
}
