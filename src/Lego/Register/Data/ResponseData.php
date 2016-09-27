<?php namespace Lego\Register\Data;

use Illuminate\Support\Facades\Request;
use Lego\Register\Register;

class ResponseData extends Data
{
    const GET_PARAM = '__lego';

    public static function url($path, array $query = [])
    {
        return Request::fullUrlWithQuery(
            array_merge($query, [self::GET_PARAM => $path])
        );
    }

    public static function add($path, \Closure $closure)
    {
        return Register::register(self::class, self::class, [$path => $closure]);
    }

    public static function response()
    {
        $path = Request::get(self::GET_PARAM);
        if (!$path) {
            return null;
        }

        $provider = Register::get(self::class, self::class);
        $response = $provider->data($path);
        if (is_null($response)) {
            return null;
        }

        return call_user_func($response, Request::all());
    }

    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param array $data
     */
    protected function validate(array $data = [])
    {
        lego_assert($this->path() === self::class, 'ResponseData Path must be self.');
    }
}