<?php

namespace Lego\Set\Form;

use Closure;

trait FormFieldAccessorAndMutator
{
    /**
     * 使用 accessor 重写从 model 获取初始值的行为
     *
     * 以 $field = addText('name', 'Name') 为例，
     * 默认情况下，输入框的默认值是 $model->name
     * 使用 accessor 重写如下：
     *      $field->accessor(function ($model, $value) {
     *          return $model->firstName;
     *      })
     * 重写后，输入框的默认值即 $model->firstName
     *
     * @var Closure|null   需要接受两个参数 ($model, $value)
     */
    private $accessor;

    /**
     * 使用 mutator 可以重写 新的输入值 赋值到 model 的方式
     *
     * 注意：
     *  - Eloquent：如果在 mutator 中修改了 model 的 related（关系）model，需要在 mutator 中自行调用 save 进行保存
     *      这是因为 form 在存储时无法判定 mutator 修改了哪些 related model
     *
     * 以 $field = addText('name', 'Name') 为例，
     * 默认情况下，输入值在保存时会存储到 $model->name 字段
     * 使用 mutator 重写如下：
     *      $field->mutator(function ($model, $value) {
     *          return $model->firstName = $value
     *      })
     * 重写后，输入值会存储到 $model->firstName 字段
     *
     * @var Closure|null   需要接受两个参数 ($model, $value)
     */
    private $mutator;

    /**
     * @return Closure|null
     */
    public function getAccessor(): ?Closure
    {
        return $this->accessor;
    }

    /**
     * @param Closure|null $accessor
     * @return $this
     */
    public function accessor(Closure $accessor)
    {
        $this->accessor = $accessor;
        return $this;
    }

    /**
     * @return Closure|null
     */
    public function getMutator(): ?Closure
    {
        return $this->mutator;
    }

    /**
     * @param Closure|null $mutator
     * @return $this
     */
    public function mutator(Closure $mutator)
    {
        $this->mutator = $mutator;
        return $this;
    }
}
