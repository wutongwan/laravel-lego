<?php namespace Lego\Field\Operators;

use Lego\Foundation\Value;

trait ValueOperator
{
    /**
     * @var Value
     */
    private $value;

    protected function initializeValueOperator()
    {
        $this->value = new Value();
    }

    public function value()
    {
        return $this->value;
    }
}