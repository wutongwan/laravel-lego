<?php namespace Lego\Field\Concerns;

use Lego\Operator\Query\Query;

trait FilterWhereEquals
{
    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Query $query
     * @return Query
     */
    public function filter(Query $query)
    {
        return $this->filterWithRelation($query, function (Query $query) {
            return $query->whereEquals($this->column(), $this->getNewValue());
        });
    }
}
