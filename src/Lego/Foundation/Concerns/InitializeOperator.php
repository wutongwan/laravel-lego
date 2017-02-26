<?php namespace Lego\Foundation\Concerns;

trait InitializeOperator
{
    /**
     * 触发函数, 推荐在构造函数中调用
     */
    protected function triggerInitialize()
    {
        /**
         * Initialize Traits
         *
         * call each trait's `initializeTraitName()` method.
         */
        foreach (class_uses_recursive(static::class) as $trait) {
            $method = 'initialize' . class_basename($trait);
            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], []);
            }
        }

        // 初始化自身
        $this->initialize();
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
    }
}
