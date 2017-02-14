<?php namespace Lego\Field\Operators;

use Lego\Data\Table\Table;
use Lego\Foundation\Exceptions\LegoException;

trait DisabledInFilter
{
    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     * @throws LegoException
     */
    public function filter(Table $query)
    {
        return $query;
    }
}
