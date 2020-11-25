<?php

namespace Lego\Input;

class Radios extends Select
{
    public function getInputType(): string
    {
        return 'radio';
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        foreach ($options as $option) {
            if (!is_scalar($option)) {
                throw new \InvalidArgumentException('Radio not support nested option');
            }
        }
        return parent::options($options);
    }

    protected function viewName(): string
    {
        return 'lego::bootstrap3.input.radios';
    }
}
