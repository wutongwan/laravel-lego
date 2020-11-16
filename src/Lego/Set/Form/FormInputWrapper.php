<?php

namespace Lego\Set\Form;

use Lego\Foundation\Message\HasMessages;
use Lego\Input\Input;
use Lego\Set\Form\Concerns\FormFieldAccessorAndMutator;
use Lego\Set\Form\Concerns\FormFieldValidations;

/**
 * Class FormInputWrapper
 * @package Lego\Foundation
 * @internal
 */
class FormInputWrapper
{
    use HasMessages,
        FormFieldValidations,
        FormFieldAccessorAndMutator;

    /**
     * @var Input
     */
    private $input;

    public function __construct(Input $input)
    {
        $this->input = $input;

        $this->initializeMessages();
    }

    public function __call($method, $parameters)
    {
        // forward calls to $input
        if (method_exists($this->input, $method)) {
            $result = call_user_func_array([$this->input, $method], $parameters);
            return $result === $this->input ? $this : $result; // 根据返回值判定是否返回 $this
        }

        throw new \BadMethodCallException($method);
    }
}
