<?php namespace Lego\Field\Plugin;

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

    /**
     * 验证当前值是否符合 rules
     *
     * 验证失败时, 报错信息会写到 $this->errors
     *
     * @return bool 是否通过验证
     */
    public function validate()
    {
        $validator = \Validator::make(
            [$this->column => $this->getNewValue()],
            [$this->column => $this->rules()],
            [],
            [$this->column => $this->description]
        );

        /** @var Field $this */

        if ($validator->fails()) {
            $this->errors()->merge($validator->messages());
            return false;
        }

        return true;
    }
}