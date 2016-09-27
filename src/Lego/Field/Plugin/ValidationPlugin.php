<?php namespace Lego\Field\Plugin;

use Illuminate\Support\Facades\Validator;
use Lego\Field\Field;

trait ValidationPlugin
{
    /**
     * 改 Field 所有 Validation
     * eg: ['required', 'email']
     */
    private $rules = [];

    public function rules() : array
    {
        return $this->rules;
    }

    public function rule($rule, $condition = true)
    {
        if (value($condition) && !in_array($rule, $this->rules)) {
            $this->rules [] = $rule;
        }
        return $this;
    }

    /**
     * 设置此字段为必填
     *
     * @param bool $condition
     * @return static
     */
    public function required($condition = true)
    {
        return $this->rule('required', $condition);
    }

    public function isRequired()
    {
        return in_array('required', $this->rules);
    }

    /**
     * 验证当前值是否符合 rules
     *
     * 验证失败时, 报错信息会写到 $this->errors
     *
     * @return bool 是否通过验证
     */
    public function validate()
    {
        if ($this->isReadonly()) {
            return true;
        }

        /**
         * ide-helper comment
         * @var Field $this
         * @var \Illuminate\Validation\Validator $validator
         */

        $validator = Validator::make(
            [$this->column => $this->value()->current()],
            [$this->column => $this->rules()],
            [],
            [$this->column => $this->description]
        );


        if ($validator->fails()) {
            $this->errors()->merge($validator->messages());
            return false;
        }

        return true;
    }

    public function validateFailed()
    {
        return !$this->validate();
    }
}