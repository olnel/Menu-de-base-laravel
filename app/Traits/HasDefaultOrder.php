<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasDefaultOrder
{
    protected static function bootHasDefaultOrder()
    {
        $column = static::$defaultOrderColumn ?? 'id';
        $direction = static::$defaultOrderDirection ?? 'desc';

        static::addGlobalScope('order', function (Builder $builder) use ($column, $direction) {
            if ($builder->getQuery()->distinct && !in_array($column, $builder->getQuery()->columns ?? [])) {
                return;
            }
            $builder->orderBy($column, $direction);
        });
    }

    public function scopeWithoutOrder($query)
    {
        return $query->withoutGlobalScope('defaultOrder');
    }
}
