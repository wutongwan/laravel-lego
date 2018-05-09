<?php namespace Lego\Tests\Field;

use Lego\Field\Field;

class ExampleField extends Field
{
    public function render()
    {
        return $this->takeInputValue();
    }
}
