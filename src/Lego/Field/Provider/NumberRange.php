<?php namespace Lego\Field\Provider;

use Lego\Field\Concerns\RangeFilterOperator;
use Lego\Field\Concerns\FilterOnly;
use Lego\Field\Field;

class NumberRange extends Field
{
    use RangeFilterOperator;
    use FilterOnly;

    const FIELD_TYPE = Number::class;

    public function renderEditable()
    {
        return $this->view('lego::default.field.range');
    }
}
