<?php namespace Lego\Field\Provider;

use Carbon\Carbon;
use Lego\Data\Table\Table;
use Lego\LegoException;

class DatetimeRange extends Datetime
{
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

    /**
     * @return Carbon[]
     */
    public function getCurrentValue()
    {
        $current = $this->value()->current();
        foreach ($current as &$item) {
            $item = $this->convertToCarbon($item);
        }
        return $current;
    }

    protected function getShowValue()
    {
        $values = $this->getCurrentValue();
        foreach ($values as &$item) {
            $item = $item ? $item->format($this->getFormat()) : null;
        }
        return $values;
    }

    protected function renderEditable(): string
    {
        return view('lego::default.field.date-range', ['field' => $this]);
    }

    public function syncCurrentValueToSource()
    {
        throw new LegoException(static::class . ' for filter only.');
    }
}