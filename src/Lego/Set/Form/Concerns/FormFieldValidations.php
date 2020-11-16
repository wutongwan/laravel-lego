<?php

namespace Lego\Set\Form\Concerns;

trait FormFieldValidations
{
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
     * 是否必填
     *
     * @var bool
     */
    private $required = false;

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
        if ($rule = $this->input->getAdaptor()->createUniqueRule()) {
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

    public function required(bool $required = true)
    {
        $this->required = $required;
        return $this;
    }

    public function isRequired()
    {
        return $this->required;
    }
}
