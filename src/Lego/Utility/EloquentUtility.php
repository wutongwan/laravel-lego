<?php

namespace Lego\Utility;

use Illuminate\Database\Eloquent\Builder;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;

class EloquentUtility
{
    public static function match(Builder $query, MatchQuery $match, string $columnName, string $keyName)
    {
        $options = $query
            ->where($columnName, 'like', "%{$match->keyword}%")
            ->limit($match->limit)
            ->offset($match->limit * max(($match->page - 1), 0))
            ->pluck($columnName, $keyName)
            ->all();

        return new MatchResults($options);
    }
}
