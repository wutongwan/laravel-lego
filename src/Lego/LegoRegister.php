<?php

namespace Lego;

use Illuminate\Support\Arr;
use Lego\Register\Data;

/**
 * Lego 内部的注册器.
 *
 * 文档见: `docs/register.md`
 */
class LegoRegister
{
    const DEFAULT_TAG = 'default';

    private static $registered = []; // register data

    /**
     * 注册函数, 推荐使用全局函数 `lego_register()`.
     *
     * @param string      $name 匹配到 `Lego/Register/Data` 目录中的注册数据类
     * @param mixed       $data 注册数据, 数组
     * @param string|null $tag
     *
     * @return \Lego\Register\Data
     */
    public static function register($dataClass, $data, $tag = self::DEFAULT_TAG)
    {
        /** @var Data $instance */
        $instance = new $dataClass($data, $tag);
        Arr::set(self::$registered, "{$dataClass}.{$tag}", $data);
        $instance->afterRegistered();

        return $instance;
    }

    /**
     * 获取特定注册项.
     */
    public static function get($dataClass, $tag, $default = null)
    {
        return Arr::get(self::$registered, "{$dataClass}.{$tag}", $default);
    }

    public static function getDefault($dataClass)
    {
        return self::get($dataClass, self::DEFAULT_TAG);
    }

    public static function getAll($dataClass, $default = null)
    {
        return Arr::get(self::$registered, $dataClass, $default);
    }
}
