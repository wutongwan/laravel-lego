<?php namespace Lego\Register\Data;

use Lego\Field\Provider\Text;
use Lego\Register\Register;

class Field extends Data
{
    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param $data
     */
    protected function validate($data)
    {
        lego_assert(
            is_subclass_of($data, \Lego\Field\Field::class),
            '$data should be subclass of ' . \Lego\Field\Field::class
        );
    }

    public function afterRegistered()
    {
        $this->name = ucfirst(camel_case($this->name));
    }

    public static function availableFields()
    {
        $fields = [];
        /** @var self $data */
        foreach (Register::get(self::class, null, []) as $data) {
            $fields [$data->name()] = $data->data();
        }
        return array_merge($fields, self::internalFields());
    }

    private static function internalFields()
    {
        static $fields = [];
        if ($fields) {
            return $fields;
        }

        foreach (scandir(__DIR__ . '/../../Field/Provider/') as $file) {
            if (!ends_with($file, '.php')) {
                continue;
            }

            $name = explode('.php', $file)[0];
            $fields [$name] = class_namespace(Text::class, $name);
        }

        return $fields;
    }

    public static function get($name)
    {
        return self::availableFields()[$name];
    }
}