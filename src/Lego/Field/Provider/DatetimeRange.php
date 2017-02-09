<?php namespace Lego\Field\Provider;

use Carbon\Carbon;
use Lego\Field\Operators\BetweenFilterTrait;
use Lego\Field\Operators\FilterOnly;

class DatetimeRange extends Datetime
{
    use BetweenFilterTrait;
    use FilterOnly;

    /**
     * @return Carbon[]
     */
    public function getCurrentValue()
    {
        $current = $this->getValue([]);
        $current['min'] = $this->convertToCarbon(array_get($current, 'min'));
        $current['max'] = $this->convertToCarbon(array_get($current, 'max'));
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
        return $this->view('lego::default.field.date-range');
    }
}
