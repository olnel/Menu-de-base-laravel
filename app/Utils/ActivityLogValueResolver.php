<?php

namespace App\Utils;

use App\Models\CarburantCard;
use App\Models\Chauffeur;
use App\Models\Client;
use App\Models\Remorque;
use App\Models\Reservation;
use App\Models\Tarif;
use App\Models\Tresorerie;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActivityLogValueResolver
{
    /**
     * field_name => [ModelClass, label_callback, display_key]
     */
    private static array $fieldMap = [
        'vehicule_id'        => [Vehicule::class,    'getVehiculeLabel',    'Véhicule'],
        'remorque_id'        => [Remorque::class,    'getRemorqueLabel',    'Remorque'],
        'chauffeur_id'       => [Chauffeur::class,   'getChauffeurLabel',   'Chauffeur'],
        'aide_chauffeur_id'  => [Chauffeur::class,   'getChauffeurLabel',   'Aide chauffeur'],
        'client_id'          => [Client::class,      'getClientLabel',      'Client'],
        'user_id'            => [User::class,        'getUserLabel',        'Utilisateur'],
        'reservation_id'     => [Reservation::class, 'getReservationLabel', 'Réservation'],
        'tarif_id'           => [Tarif::class,       'getTarifLabel',       'Tarif'],
        'tresorerie_id'      => [Tresorerie::class,  'getTresorerieLabel',  'Trésorerie'],
        'carburant_card_id'  => [CarburantCard::class,'getCarburantCardLabel','Carte carburant'],
    ];

    public function resolveForData(mixed $data): mixed
    {
        if ($data instanceof LengthAwarePaginator) {
            $items = $data->getCollection();
            $cache = $this->buildCache($items);
            $items->transform(fn($log) => $this->transformLog($log, $cache));
            return $data;
        }

        if ($data instanceof Collection) {
            $cache = $this->buildCache($data);
            return $data->map(fn($log) => $this->transformLog($log, $cache));
        }

        return $data;
    }

    private function buildCache(Collection $items): array
    {
        $idsByField = array_fill_keys(array_keys(self::$fieldMap), []);

        foreach ($items as $log) {
            foreach (['old_values', 'new_values'] as $prop) {
                $values = $log->{$prop};
                if (!is_array($values)) continue;
                foreach ($values as $key => $value) {
                    if (isset(self::$fieldMap[$key]) && $value !== null) {
                        $idsByField[$key][] = $value;
                    }
                }
            }
        }

        $cache = [];
        foreach (self::$fieldMap as $field => [$modelClass, $labelMethod]) {
            $ids = array_values(array_unique(array_filter($idsByField[$field])));
            if (empty($ids)) continue;
            $cache[$field] = $modelClass::whereIn('id', $ids)
                ->get()
                ->keyBy('id')
                ->map(fn($m) => self::{$labelMethod}($m));
        }

        return $cache;
    }

    private function transformLog(mixed $log, array $cache): mixed
    {
        $log->old_values = $this->resolveValues($log->old_values ?? [], $cache);
        $log->new_values = $this->resolveValues($log->new_values ?? [], $cache);
        return $log;
    }

    private function resolveValues(array $values, array $cache): array
    {
        $result = [];
        foreach ($values as $key => $value) {
            if (isset(self::$fieldMap[$key])) {
                [, , $displayKey] = self::$fieldMap[$key];
                $result[$displayKey] = $cache[$key][$value] ?? $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    // ── Label builders ────────────────────────────────────────────────────────

    private static function getVehiculeLabel(Vehicule $m): string
    {
        return trim($m->marque . ' ' . $m->modele) . ' (' . $m->immatriculation . ')';
    }

    private static function getRemorqueLabel(Remorque $m): string
    {
        return $m->numero_remorque ?? "Remorque #{$m->id}";
    }

    private static function getChauffeurLabel(Chauffeur $m): string
    {
        return strtoupper($m->nom) . ' ' . ucfirst($m->prenom ?? '');
    }

    private static function getClientLabel(Client $m): string
    {
        return $m->nom_client ?? "Client #{$m->id}";
    }

    private static function getUserLabel(User $m): string
    {
        return $m->name ?? "User #{$m->id}";
    }

    private static function getReservationLabel(Reservation $m): string
    {
        return $m->numero_reservation ?? "Réservation #{$m->id}";
    }

    private static function getTarifLabel(Tarif $m): string
    {
        return $m->nom_tarif ?? "Tarif #{$m->id}";
    }

    private static function getTresorerieLabel(Tresorerie $m): string
    {
        return $m->nom_tresorerie ?? "Trésorerie #{$m->id}";
    }

    private static function getCarburantCardLabel(CarburantCard $m): string
    {
        return $m->numero_carte ?? "Carte #{$m->id}";
    }
}
