<?php

namespace Lego\Tests\Widget\Grid;

use Lego\Widget\Grid\Pipes;

class ExamplePipes extends Pipes
{
    public function handleAlwaysHelloLego()
    {
        return 'hello lego';
    }

    public function handleAlways($what)
    {
        return $what;
    }

    public function handleIncrement()
    {
        return $this->value() + 1;
    }

    public function handleIncrementBy($step = 1)
    {
        return $this->value() + $step;
    }

    public function handleReturnAttributeCode()
    {
        return data_get($this->data(), 'code');
    }

    public function handleReturnCellDescription()
    {
        return $this->cell()->description();
    }
}
