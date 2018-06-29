<?php

namespace Lego\Field\Concerns;

use Lego\Operator\Query;

trait DisabledInFilter
{
    /**
     * Filter 检索数据时, 构造此字段的查询.
     */
    public function filter(Query $query)
    {
        return $query;
    }
}
