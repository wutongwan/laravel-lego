<?php namespace Lego\Register;

use Lego\Register\Data\Data as RegisterData;
use Lego\Register\Data\Data;

/**
 * Lego 内部的注册器
 *
 * 文档见: `docs/register.md`
 */
class Register
{
    private static $registered = []; // register data

    /**
     * 注册函数, 推荐使用全局函数 `lego_register()`
     *
     * @param string $name 匹配到`Lego/Register/Data`目录中的注册数据类, 支持两种格式:
     *      1、\Lego\Register\Data\FieldData::class, 类的全名
     *      2、`field.data`, 类名, 以点号分隔, eg: field.data => FieldData
     * @param mixed $data 注册数据, 数组
     * @param string|null $dataName 源数据类型, eg: \Room::class
     * @return RegisterData
     */
    public static function register($name, $data = null, $dataName = null)
    {
        $key = $dataClass = self::translateClass($name);

        if (!is_null($dataName)) {
            $dataName = self::translateDataName($dataName);
            $key = self::key($dataClass, $dataName);
        }

        /** @var Data $instance */
        $instance = new $dataClass($data, $dataName);
        array_set(self::$registered, $key, $instance);
        $instance->afterRegistered();

        return $instance;
    }

    /**
     * 所有注册数据
     * @return array
     */
    public static function registered()
    {
        return self::$registered;
    }

    /**
     * 获取特定注册项
     *
     * @param $name
     * @param string|null $dataName
     * @param mixed $default
     * @return RegisterData
     */
    public static function get($name, $dataName = null, $default = null)
    {
        $key = self::translateClass($name);
        if (!is_null($dataName)) {
            $key = self::key($key, self::translateDataName($dataName));
        }
        return array_get(self::registered(), $key, $default);
    }

    /**
     * key 转换为 RegisterData 类
     *
     * - field.data => \Lego\Register\Data\FieldData
     * - \Lego\Register\Data\FieldData => \Lego\Register\Data\FieldData
     *
     * @param $key
     * @return mixed|string
     */
    private static function translateClass($key)
    {
        if (is_subclass_of($key, RegisterData::class)) {
            return $key;
        }

        static $cache = [];

        // 避免多次进行父类判定
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        // abc.def.ghi => AbcDefGhi
        $classBaseName = ucfirst(camel_case(str_replace('.', '_', $key)));
        $class = class_namespace(RegisterData::class, $classBaseName);

        lego_assert(
            is_subclass_of($class, RegisterData::class),
            "Unsupported Register data {$class}(key: {$key})"
        );

        $cache[$key] = $class;

        return $class;
    }

    private static function translateDataName($path)
    {
        return is_object($path) ? get_class($path) : $path;
    }

    /**
     * 根据 RegisterData::class 和 path 拼接出 注册数据的 key
     */
    private static function key($class, $path)
    {
        return join('.', func_get_args());
    }
}