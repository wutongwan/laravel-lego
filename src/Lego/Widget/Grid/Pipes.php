<?php

namespace Lego\Widget\Grid;

abstract class Pipes
{
    private $value;
    private $data;
    /**
     * @var Cell
     */
    private $cell;

    public function __construct($value, $data, Cell $cell)
    {
        $this->value = $value;
        $this->data = $data;
        $this->cell = $cell;
    }

    protected function value()
    {
        return $this->value;
    }

    protected function data()
    {
        return $this->data;
    }

    protected function cell()
    {
        return $this->cell;
    }

    /**
     * @throws PipeBreakException
     */
    protected function break($value = null)
    {
        throw new PipeBreakException(is_null($value) ? $this->value() : $value);
    }
}
