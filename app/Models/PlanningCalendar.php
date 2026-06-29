<?php

namespace App\Models;

use App\Traits\HasDefaultOrder;
use App\Traits\HasNotifications;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PlanningCalendar extends Basemodel
{
    use HasFactory;
    use HasDefaultOrder;
    use HasNotifications;
    protected $appends = ['immatriculation', 'libelle', 'notification', 'background', 'text_color'];
    protected static string $defaultOrderColumn = 'date_maintenance';
    protected static string $defaultOrderDirection = 'ASC';
    protected static function boot()
    {
        parent::boot();
        static::bootHasDefaultOrder();
    }
    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                // Champs directs de la table PlanningCalendar
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhereDate('date_maintenance', $search);

                // Champs du véhicule lié
                $q->orWhereHas('vehicule', function ($subQuery) use ($search) {
                    $subQuery->where('immatriculation', 'LIKE', "%$search%");
                });

                // Champs du libellé maintenance lié
                $q->orWhereHas('libelleMaintenance', function ($subQuery) use ($search) {
                    $subQuery->where('libelle', 'LIKE', "%$search%")
                                ->orWhere('background', 'LIKE', "%$search%")
                                ->orWhere('text_color', 'LIKE', "%$search%")
                                ->orWhere('date_maintenance', 'LIKE', "%$search%");
                });
            });
        }

        return $query;
    }


    public function scopeFetchdate($query)
    {
        $request = app(Request::class);

        if (!$request->has('date_filtre') || empty($request->date_filtre)) {
            $date_filtre = date('Y-m-d');
        } else {
            $date_filtre = $request->date_filtre;
        }

        $startOfMonth = date('Y-m-01', strtotime($date_filtre));
        $endOfMonth = date('Y-m-t', strtotime($date_filtre));

        $query->where(function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('date_maintenance', [$startOfMonth, $endOfMonth]);;
        });

        return $query;
    }


    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class,'vehicule_id');
    }

    public function libelleMaintenance()
    {
        return $this->belongsTo(LibelleMaintenance::class,'libelle_maintenance_id');
    }

    /**
     * Vérifie si la maintenance doit être notifiée aujourd'hui
     */
    public function shouldNotifyToday(): bool
    {
        if (!$this->libelleMaintenance || $this->libelleMaintenance->notification <= 0) {
            return false;
        }

        $today = Carbon::today();
        $notificationStart = $this->libelleMaintenance->getNotificationStartDate($this->date_maintenance);

        return $today->between($notificationStart, $this->date_maintenance);
    }

    /**
     * Calcule les jours restants avant maintenance
     */
    public function getDaysRemaining(): int
    {
        return Carbon::today()->diffInDays($this->date_maintenance);
    }

    public function toArray()
    {
        $default = parent::toArray();
        $default['immatriculation'] = $this->vehicule?->immatriculation;
        $default['libelle'] = $this->libelleMaintenance?->libelle;
        $default['notification'] = $this->libelleMaintenance?->notification;
        $default['background'] = $this->libelleMaintenance?->background;
        $default['text_color'] = $this->libelleMaintenance?->text_color;

        return $default;
    }

    public function toJson($options = 0)
    {
        $default = parent::toArray();
        // $default['immatriculation'] = $this->vehicule?->immatriculation;
        // $default['libelle'] = $this->libelleMaintenance?->libelle;
        // $default['notification'] = $this->libelleMaintenance?->notification;
        // $default['background'] = $this->libelleMaintenance?->background;
        // $default['text_color'] = $this->libelleMaintenance?->text_color;

        return $default;
    }

    public function getImmatriculationAttribute(){
        return  $this->vehicule?->immatriculation;
    }
    public function getLibelleAttribute(){
        return  $this->libelleMaintenance?->libelle;
    }
    public function getNotificationAttribute(){
        return  $this->libelleMaintenance?->notification;
    }
    public function getBackgroundAttribute(){
        return  $this->libelleMaintenance?->background;
    }
    public function getTextColorAttribute(){
        return  $this->libelleMaintenance?->text_color;
    }
}
