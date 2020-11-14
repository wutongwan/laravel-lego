<?php

namespace Lego\Set\Form;

use Lego\DataAdaptor\DataAdaptor;
use Lego\Foundation\FieldName;
use Lego\Foundation\Message\HasMessages;
use Lego\Input\Input;
use Lego\Set\Form\Concerns\FormFieldAccessorAndMutator;
use Lego\Set\Form\Concerns\FormFieldValidations;

/**
 * Class FormField
 * @package Lego\Foundation
 * @internal
 */
class FormField
{
    use HasMessages;
    use FormFieldValidations;
    use FormFieldAccessorAndMutator;

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var DataAdaptor
     */
    protected $adaptor;

    public function __construct(Input $input, FieldName $fieldName, string $label, DataAdaptor $adaptor)
    {
        $this->initializeHasMessages();

        $this->adaptor = $adaptor;

        $input->setLabel($label);
        $input->setAdaptor($adaptor);
        $input->setFieldName($fieldName);
        $input->initializeHook();
        $this->input = $input;
    }

    /**
     * @return Input
     */
    public function getInput(): Input
    {
        return $this->input;
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
