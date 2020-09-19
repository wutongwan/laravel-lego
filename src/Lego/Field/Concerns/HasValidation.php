<?php

namespace Lego\Field\Concerns;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Lego\Field\Field;
use Lego\Foundation\Exceptions\LegoException;
use Lego\LegoRegister;
use Lego\Register\FieldSelfDefinedValidation;

trait HasValidation
{
    /**
     * 改 Field 所有 Validation
     * eg: ['required', 'email'].
     */
    protected $rules = [];
    protected $discardedRules = [];

    public function rules(): array
    {
        return $this->rules;
    }

    public function rule($rule, $condition = true)
    {
        if (!value($condition)) {
            return $this;
        }

        if (!Str::contains($rule, 'regex') && Str::contains($rule, '|')) {
            foreach (explode('|', $rule) as $item) {
                $this->rule($item);
            }

            return $this;
        }

        if (!in_array($rule, $this->rules) && !in_array($rule, $this->discardedRules)) {
            $this->rules[] = $rule;
        }

        return $this;
    }

    public function removeRule($rule)
    {
        if (($key = array_search($rule, $this->rules)) !== false) {
            unset($this->rules[$key]);
        }
        $this->discardedRules[] = $rule;

        return $this;
    }

    /**
     * 设置此字段为必填.
     *
     * @param bool $condition
     *
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
     * 自定义的 validator closure.
     *
     * @var \Closure[]
     */
    protected $validators = [];

    /**
     * 对 input 值的自定义校验，支持调用多次
     *
     * @param string|\Closure $validator
     * @param bool $condition
     *
     * @return $this
     */
    public function validator($validator, $condition = true)
    {
        if (!$condition) {
            return $this;
        }

        if ($validator instanceof \Closure) {
            $this->validators[] = $validator;
        } else {
            $this->rule($validator);
        }

        return $this;
    }

    /**
     * 验证当前值是否符合 rules.
     *
     * 验证失败时, 报错信息会写到 $this->errors
     *
     * @return bool 是否通过验证
     */
    public function validate($data = [])
    {
        if ($this->isReadonly()) {
            return true;
        }

        $registered = LegoRegister::getDefault(FieldSelfDefinedValidation::class);
        if ($registered) {
            call_user_func_array($registered, [$this, $this->data]);
        }

        $value = $this->getNewValue();

        /**
         * Run Laravel Validation
         * ide-helper comment.
         *
         * @var Field
         * @var \Illuminate\Validation\Validator $validator
         */
        $this->isRequired() || $this->rule('nullable');
        $validator = Validator::make(
            $data ?: [$this->elementName() => $value],
            [$this->elementName() => $this->rules()],
            [],
            [$this->elementName() => $this->description()]
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
     * Laravel Validation unique.
     *
     * Auto except current model
     *
     * https://laravel.com/docs/master/validation#rule-unique
     */
    public function unique($id = null, $idColumn = null, $extra = null)
    {
        if (!$this->store instanceof \Lego\Operator\Eloquent\EloquentStore) {
            throw new LegoException(
                'Validation: `unique` rule only worked for Eloquent, ' .
                'you can use `validator($closure)` implement unique validation.'
            );
        }

        /**
         * @var \Illuminate\Database\Eloquent\Model
         * @var Field $this
         */
        $model = $this->data;

        $id = $id ?: $this->store->getKey() ?: 'NULL';
        $idColumn = $idColumn ?: $this->store->getKeyName();

        $parts = [
            "unique:{$model->getConnectionName()}.{$model->getTable()}",
            $this->column(),
            $id,
            $idColumn,
        ];

        if ($extra) {
            $parts[] = trim($extra, ',');
        }

        $this->rule(join(',', $parts));

        return $this;
    }
}
