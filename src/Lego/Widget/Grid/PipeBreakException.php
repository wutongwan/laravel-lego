<?php

namespace Lego\Widget\Grid;

use Lego\Foundation\Exceptions\LegoException;

class PipeBreakException extends LegoException
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
