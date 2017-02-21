<?php namespace Lego\Field\Concerns;

use Lego\Operator\Query\Query;

trait BetweenFilterTrait
{
    /**
     * Filter 检索数据时, 构造此字段的查询
     */
    public function filter(Query $query)
    {
        $values = $this->getNewValue();
        $min = array_get($values, 'min');
        $max = array_get($values, 'max');

        switch (true) {
            case $min && $max:
                return $query->whereBetween($this->column(), $min, $max);

            case $min:
                return $query->whereGte($this->column(), $min);

            case $max:
                return $query->whereLte($this->column(), $max);

            default:
                return $query;
        }
    }
}
