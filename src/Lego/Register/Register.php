<?php namespace Lego\Register;

abstract class Register
{
    private static $registered = []; // register data

    private static function name()
    {
        return class_basename(static::class);
    }

    public static function register($data)
    {
        static::validate($data);

        $name = self::name();

        if (!isset(self::$registered[$name])) {
            self::$registered [$name] = [];
        }
        self::$registered [$name][] = $data;
    }

    /**
     * 验证注册的数据是否合法
     */
    protected static function validate($data)
    {
    }

    public static function registered()
    {
        return array_get(self::$registered, self::name(), []);
    }
}