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
     * @param string $key 匹配到`Lego/Register/Data`目录中的注册数据类, 支持两种格式:
     *      1、\Lego\Register\Data\FieldData::class, 类的全名
     *      2、`field.data`, 类名, 以点号分隔, eg: field.data => FieldData
     * @param string|null $type 源数据类型, eg: \Room::class
     * @param array $data 注册数据, 数组
     * @return RegisterData
     */
    public static function register($key, $type = null, array $data = [])
    {
        $class = self::dataClass($key);

        /** @var RegisterData $provider */
        $provider = new $class($type, $data);
        array_set(static::$registered, self::registerKey($class, $type), $provider);
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
     * @param $key
     * @param null $type
     * @return mixed
     */
    public static function get($key, $type = null)
    {
        return array_get(
            self::$registered,
            self::registerKey(self::dataClass($key), $type),
            []
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
    private static function dataClass($key)
    {
        if (is_subclass_of(RegisterData::class, $key)) {
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

    /**
     * 根据 RegisterData::class 和 type 拼接出 注册数据的 key
     */
    private static function registerKey($class, $type)
    {
        return $class . '.' . $type;
    }
}