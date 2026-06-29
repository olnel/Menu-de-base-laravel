<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DocumentChauffeur extends Basemodel
{
    use HasFactory;
    protected $table = 'documents_chauffeurs';
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public function toArray(): array
    {
        $default = parent::toArray();
        $default['fichier_jointe'] = json_decode($this->fichier_jointe) ?? '';
        $default['date_expiration'] = $this->date_expiration
            ? Carbon::parse($this->date_expiration)->format('d-m-Y')
            : null;

        return $default;
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}
