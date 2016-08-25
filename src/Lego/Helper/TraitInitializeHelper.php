<?php namespace Lego\Helper;

/**
 * trait 初始化组件
 *  - 功能: 调用当前类use的trait中定义的 `initializeTraitName()`
 *  - 推荐在构造函数最后调用下面函数 `initializeTraits()`
 *
 * Class TraitInitializeHelper
 * @package Lego\Helper
 */
trait TraitInitializeHelper
{
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