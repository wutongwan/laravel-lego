<?php namespace Lego\Register\Data;

use Lego\Foundation\Exceptions\InvalidRegisterData;
use Lego\Register\Register;

class UserDefinedField extends Data
{
    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
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
        if ($this->tag === Register::DEFAULT_TAG) {
            lego_register(self::class, $this->data, class_basename($this->data));
        }
    }

    /**
     * list user defined fields
     *
     * @return array
     */
    public static function list()
    {
        $fields = Register::getAll(self::class);
        unset($fields[Register::DEFAULT_TAG]);
        return $fields;
    }
}
