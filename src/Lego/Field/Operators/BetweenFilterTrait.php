<?php namespace Lego\Field\Operators;

use Lego\Data\Table\Table;

trait BetweenFilterTrait
{
    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     */
    public function filter(Table $query): Table
    {
        $values = $this->getCurrentValue();
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