<?php namespace Lego\Register;

/**
 * 在Lego外部创建自定义的Field时, 通过此注册器加入Lego可识别的Field列表
 */
class FieldRegister extends Register
{
    protected static function validate($field)
    {
        assert(class_exists($field), "Field Class [{$field}] Not Exists.");
    }
}