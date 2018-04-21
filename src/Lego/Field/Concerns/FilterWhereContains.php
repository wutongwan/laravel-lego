<?php namespace Lego\Field\Concerns;

use Lego\Operator\Query;

trait FilterWhereContains
{
    public function filter(Query $query)
    {
        return $query->whereContains($this->name(), $this->getNewValue());
    }
}
