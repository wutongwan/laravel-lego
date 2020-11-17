<?php

namespace Lego\Set\Common;

use Lego\Input\Input;

trait HasFields
{
    private function callAddField(string $method, array $parameters)
    {
        if (str_starts_with($method, 'add')) {
            $inputBaseClassName = substr($method, 3);
            if ($inputBaseClassName
                && class_exists($inputClass = "Lego\\Input\\{$inputBaseClassName}")
                && is_subclass_of($inputClass, Input::class)
            ) {
                return $this->addField($inputClass, ...$parameters);
            }
        }

        return null;
    }

    abstract protected function addField($inputClass, ...$parameters);
}
