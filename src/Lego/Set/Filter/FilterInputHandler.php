<?php

namespace Lego\Set\Filter;

use Lego\Input\Input;

abstract class FilterInputHandler
{
    /**
     * @var Input
     */
    protected $input;

    /**
     * @var FilterInputWrapper
     */
    protected $wrapper;

    public function __construct(Input $input, FilterInputWrapper $wrapper)
    {
        $this->input = $input;
        $this->wrapper = $wrapper;
    }

    public function afterAdd()
    {
    }

    public function query(string $operator, $value)
    {
        $this->wrapper->getAdaptor()->where(
            $this->input->getFieldName(),
            $operator,
            $value
        );
    }
}
