<?php namespace Lego\Field\Provider;

use Carbon\Carbon;
use Lego\Field\Operators\BetweenFilterTrait;
use Lego\Field\Operators\FilterOnly;

class DatetimeRange extends Datetime
{
    use BetweenFilterTrait;
    use FilterOnly;

    public function setCurrentValue($value)
    {
        $value['min'] = $this->convertToCarbon(array_get($value, 'min'));
        $value['max'] = $this->convertToCarbon(array_get($value, 'max'));

        $this->currentValue = $value;
    }

    public function getDisplayValue()
    {
        $values = $this->getCurrentValue();
        /** @var Carbon $item */
        foreach ($values as &$item) {
            $item = $item ? $item->format($this->getFormat()) : null;
        }
        return $values;
    }

    protected function renderEditable()
    {
        return $this->view('lego::default.field.date-range');
    }
}
