<?php

namespace Lego\Set\Form;

use Lego\DataAdaptor\DataAdaptor;
use Lego\DataAdaptor\EloquentAdaptor;
use Lego\Foundation\FieldName;
use Lego\Foundation\Message\HasMessages;
use Lego\Input\Input;

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
    private $input;

    /**
     * @var FieldName
     */
    private $fieldName;

    /**
     * @var string
     */
    private $fieldLabel;

    /**
     * @var EloquentAdaptor
     */
    private $adaptor;

    public function __construct(Input $input, FieldName $fieldName, string $fieldLabel, DataAdaptor $adaptor)
    {
        $this->input = $input;
        $this->input->setLabel($fieldLabel);

        $this->fieldName = $fieldName;
        $this->fieldLabel = $fieldLabel;
        $this->adaptor = $adaptor;

        $this->initializeHasMessages();
    }

    public function getFieldName(): FieldName
    {
        return $this->fieldName;
    }

    public function isEditable()
    {
        return $this->input->isReadonly() === false && $this->input->isDisabled() === false;
    }

    public function isRequired()
    {
        return true;
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
