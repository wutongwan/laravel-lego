<?php

namespace Lego\Foundation;

use Lego\Input\Input;

class FormField
{
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

    public function __construct(Input $input, FieldName $fieldName, string $fieldLabel)
    {
        $this->input = $input;
        $this->input->setLabel($fieldLabel);

        $this->fieldName = $fieldName;
        $this->fieldLabel = $fieldLabel;
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->input, $method)) {
            call_user_func_array([$this->input, $method], $parameters);
            return $this;
        }
        throw new \BadMethodCallException($method);
    }
}
