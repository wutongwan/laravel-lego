<?php namespace Lego\Foundation\Operators;

trait InitializeOperator
{
    /**
     * 触发函数, 推荐在构造函数中调用
     */
    protected function triggerInitialize()
    {
        // 初始化自身
        $this->initialize();

        // 调用 Trait 中的初始化函数
        $this->initializeTraits();
    }

    /**
     * 初始化对象
     */
    abstract protected function initialize();

    /**
     * 初始化插件
     *
     * 如果插件实现了 initializePluginName() 函数, 会在此处调用
     */
    protected function initializeTraits()
    {
        foreach (class_uses_recursive(static::class) as $trait) {
            $method = 'initialize' . class_basename($trait);
            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], []);
            }
        }
    }
}