<?php

namespace Lego\Register;

use Lego\Foundation\Exceptions\InvalidRegisterData;
use Lego\LegoRegister;

class UserDefinedField extends Data
{
    /**
     * 校验注册的数据是否合法, 不合法时抛出异常.
     *
     * @param $data
     */
    protected function validate($data)
    {
        InvalidRegisterData::assert(
            is_subclass_of($data, \Lego\Field\Field::class),
            '$data should be subclass of ' . \Lego\Field\Field::class
        );
    }

    public function afterRegistered()
    {
        if ($this->tag === LegoRegister::DEFAULT_TAG) {
            lego_register(self::class, $this->data, class_basename($this->data));
        }
    }

    /**
     * list user defined fields.
     *
     * @return array
     */
    public static function list()
    {
        $fields = LegoRegister::getAll(self::class, []);
        unset($fields[LegoRegister::DEFAULT_TAG]);

        return $fields;
    }

    private static $registered = false;

    public static function registerFromConfiguration()
    {
        if (self::$registered) {
            return;
        }

        foreach (config('lego.user-defined-fields') as $field) {
            lego_register(UserDefinedField::class, $field);
        }

        self::$registered = true;
    }
}
