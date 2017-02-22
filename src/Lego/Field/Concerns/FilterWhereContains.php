<?php namespace Lego\Field\Concerns;

use Lego\Operator\Query\Query;

trait FilterWhereContains
{
    public function filter(Query $query)
    {
        return $this->filterWithRelation($query, function (Query $query) {
            return $query->whereContains($this->column(), $this->getNewValue());
        });
    }
}
