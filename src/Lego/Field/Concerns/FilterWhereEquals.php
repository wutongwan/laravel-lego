<?php namespace Lego\Field\Concerns;

use Lego\Operator\Query;

trait FilterWhereEquals
{
    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Query $query
     * @return Query
     */
    public function filter(Query $query)
    {
        return $query->whereEquals($this->name(), $this->getNewValue());
    }
}
