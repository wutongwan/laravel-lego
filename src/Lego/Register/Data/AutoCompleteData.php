<?php namespace Lego\Register\Data;

use Lego\Register\Register;

class AutoCompleteData extends Data
{
    public static function global($name, \Closure $callable)
    {
        return Register::register(self::class, self::class, [$name => $callable]);
    }

    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param array $data
     */
    protected function validate(array $data = [])
    {
    }

    public function afterRegistered()
    {
        $globals = Register::get(self::class, self::class);

        foreach ($this->data() as $name => &$callable) {
            if ($name === self::class) {
                continue;
            }

            if (is_string($callable) && array_key_exists($callable, $globals)) {
                $callable = $globals[$callable];
                lego_assert(is_callable($callable), '$callable is not callable.');
            }

            // Register to ResponseData
            ResponseData::add($name, $callable);
        }
    }
}