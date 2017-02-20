<?php namespace Lego\Field\Concerns;

use Illuminate\Support\Facades\Validator;
use Lego\Field\Field;
use Lego\Foundation\Exceptions\LegoException;

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
    public function validator(\Closure $validator)
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
    public function validate()
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

    /**
     * Laravel Validation unique
     *
     * Auto except current model
     *
     * https://laravel.com/docs/master/validation#rule-unique
     */
    public function unique($id = null, $idColumn = null, $extra = null)
    {
        if (!$this->store instanceof \Lego\Operator\Store\EloquentStore) {
            throw new LegoException(
                'Validation: `unique` rule only worked for Eloquent, ' .
                'you can use `validator($closure)` implement unique validation.'
            );
        }

        /**
         * @var \Illuminate\Database\Eloquent\Model $model
         * @var Field $this
         */
        $model = $this->data;

        $id = $id ?: $this->store->getKey() ?: 'NULL';
        $idColumn = $idColumn ?: $this->store->getKeyName();

        $parts = [
            "unique:{$model->getConnectionName()}.{$model->getTable()}",
            $this->column(),
            $id,
            $idColumn
        ];

        if ($extra) {
            $parts [] = trim($extra, ',');
        }

        $this->rule(join(',', $parts));

        return $this;
    }
}
