<?php namespace Lego\Field\Operators;

use Illuminate\Support\Facades\Validator;
use Lego\Field\Field;

trait ValidationOperator
{
    /**
     * 改 Field 所有 Validation
     * eg: ['required', 'email']
     */
    private $rules = [];

    public function rules(): array
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
     * 自定义的 validator closure
     * @var \Closure[]
     */
    private $validators = [];

    /**
     * 对 input 值的自定义校验，支持调用多次
     * @param \Closure $validator
     * @return $this
     */
    public function validate(\Closure $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * 验证当前值是否符合 rules
     *
     * 验证失败时, 报错信息会写到 $this->errors
     *
     * @return bool 是否通过验证
     */
    public function validationPassed()
    {
        if ($this->isReadonly()) {
            return true;
        }

        $value = $this->getCurrentValue();

        /**
         * 检查 Laravel Validation
         * ide-helper comment
         * @var Field $this
         * @var \Illuminate\Validation\Validator $validator
         */

        $validator = Validator::make(
            [$this->column => $value],
            [$this->column => $this->rules()],
            [],
            [$this->column => $this->description]
        );


        if ($validator->fails()) {
            $this->errors()->merge($validator->messages());
            return false;
        }

        // 调用自定义的 Validators
        foreach ($this->validators as $closure) {
            $error = call_user_func($closure, $value);
            if (is_string($error)) {
                $this->errors()->add('error', $error);
                return false;
            }
        }

        return true;
    }
}