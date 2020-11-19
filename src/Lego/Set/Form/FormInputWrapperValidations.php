<?php

namespace Lego\Set\Form;

use Closure;

trait FormInputWrapperValidations
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
     * @param Closure(scalar, array):void $validator accept two argument: this input's value, all input's value
     * @return $this
     */
    public function validator(Closure $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }
}
