<?php

namespace Lego\Foundation\Concerns;

trait InitializeOperator
{
    /**
     * 触发函数, 推荐在构造函数中调用.
     */
    protected function triggerInitialize()
    {
        /*
         * Initialize Traits
         *
         * call each trait's `initializeTraitName()` method.
         */
        foreach ($this->listTraits() as $trait) {
            $method = 'initialize' . class_basename($trait);
            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], []);
            }
        }

        // 初始化自身
        $this->initialize();
    }

    /**
     * 列出当前类所有引入的 trait.
     *
     * laravel class_uses_recursive 输出的顺序并不是引入的顺序，不符合预期
     *
     * @return array
     */
    protected function listTraits()
    {
        $result = [];

        $classes = array_reverse(class_parents(static::class)) + [static::class];
        foreach ($classes as $class) {
            $result += trait_uses_recursive($class);
        }

        return array_unique($result);
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
    }
}
