<?php namespace Lego\Register;

use Lego\Register\Data\Data as RegisterData;

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
     * @param string $class 匹配到`Lego/Register/Data`目录中的注册数据类, 支持两种格式:
     *      1、\Lego\Register\Data\FieldData::class, 类的全名
     *      2、`field.data`, 类名, 以点号分隔, eg: field.data => FieldData
     * @param string|null $path 源数据类型, eg: \Room::class
     * @param mixed $data 注册数据, 数组
     * @return RegisterData
     */
    public static function register($class, $path = null, $data = null)
    {
        $class = self::translateClass($class);
        $path = self::translatePath($path);

        if ($provider = self::get($class, $path)) {
            $provider->merge($data);
        } else {
            $provider = new $class($path, $data);
            array_set(self::$registered, self::key($class, $path), $provider);
        }

        $provider->afterRegistered();

        return $provider;
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
     * @param $class
     * @param string|null $path
     * @param mixed $default
     * @return RegisterData
     */
    public static function get($class, $path = null, $default = [])
    {
        return array_get(
            self::registered(),
            self::key(self::translateClass($class), self::translatePath($path)),
            $default
        );
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
        if (isset($cache[$key])) {
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

    private static function translatePath($path)
    {
        return is_object($path) ? get_class($path) : $path;
    }

    /**
     * 根据 RegisterData::class 和 type 拼接出 注册数据的 key
     */
    private static function key($class, $path)
    {
        return join('.', func_get_args());
    }
}