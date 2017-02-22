<?php namespace Lego\Field\Provider;

use Lego\Field\Concerns\RangeFilterOperator;
use Lego\Field\Concerns\FilterOnly;
use Lego\Field\Field;

class DatetimeRange extends Field
{
    use RangeFilterOperator;
    use FilterOnly;

    const FIELD_TYPE = Datetime::class;

    protected function renderEditable()
    {
        return $this->view('lego::default.field.range');
    }
}
