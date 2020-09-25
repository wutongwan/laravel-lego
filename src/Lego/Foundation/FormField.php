<?php

namespace Lego\Foundation;

use Lego\DataAdaptor\DataAdaptor;
use Lego\DataAdaptor\EloquentAdaptor;
use Lego\Input\Input;

/**
 * Class FormField
 * @package Lego\Foundation
 * @internal
 */
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

    /**
     * @var Messages
     */
    private $messages;

    /**
     * laravel validation rules
     * @var array
     */
    private $rules = [];

    /**
     * user defined validators
     * @var array
     */
    private $validators = [];
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

        $this->messages = new Messages();
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

    /**
     * @return Messages|Message[]
     */
    public function messages(): Messages
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * add laravel rule
     * @return $this
     */
    public function rule($rule)
    {
        if (is_string($rule) && str_contains($rule, '|') && !str_contains($rule, 'regex')) {
            foreach (explode('|', $rule) as $item) {
                $this->rules[] = $item;
            }
        } else {
            $this->rules[] = $rule;
        }
        return $this;
    }

    public function unique()
    {
        if ($rule = $this->adaptor->createUniqueRule()) {
            $this->rule($rule);
        }
    }

    /**
     * @return array
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * add customer validator
     *
     * @return $this
     */
    public function validator(\Closure $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->input, $method)) {
            $result = call_user_func_array([$this->input, $method], $parameters);
            return $result === $this->input ? $this : $result; // 根据返回值判定是否返回 $this
        }

        throw new \BadMethodCallException($method);
    }
}
