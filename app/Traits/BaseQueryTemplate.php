<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BaseQueryTemplate
{
    /**
     * @param Builder $query
     * @param array $filter
     * @param array $filterScope
     * @return Builder
     */
    public static function apply(Builder $query, array $filter, array $filterScope = []): Builder
    {
        foreach ($filterScope as $scope => $key){
            if (array_key_exists($key, $filter) && !empty($filter[$key])){
                $query->{$scope}($filter[$key]);
            }

        }
        return  $query;
    }
}
