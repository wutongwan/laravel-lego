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
            ->limit($match->limit)
            ->offset($match->limit * max(($match->page - 1), 0))
            ->paginate($match->limit, $columns, 'page', $match->page);

        $results = new MatchResults();
        $results->setTotalCount($paginator->total());
        foreach ($paginator->items() as $item) {
            $results->add($item[$keyName], $item[$columnName]);
        }
        return $results;
    }
}
