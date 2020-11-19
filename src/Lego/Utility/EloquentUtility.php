<?php

namespace Lego\Utility;

use Illuminate\Database\Eloquent\Builder;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;

class EloquentUtility
{
    public static function match(Builder $query, MatchQuery $match, string $columnName, string $keyName)
    {
        if ($columnName === $keyName) {
            $columns = [$columnName];
            $query->distinct();
        } else {
            $columns = [$columnName, $keyName];
        }

        $paginator = $query
            ->where($columnName, 'like', "%{$match->keyword}%")
            ->simplePaginate($match->perPage, $columns, 'page', $match->page);

        $results = new MatchResults([], $paginator->hasMorePages());
        foreach ($paginator->items() as $item) {
            $results->add($item[$keyName], $item[$columnName]);
        }
        return $results;
    }
}
