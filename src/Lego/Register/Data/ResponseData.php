<?php namespace Lego\Register\Data;

use Illuminate\Support\Facades\Request;
use Lego\Register\Register;

class ResponseData extends Data
{
    const REQUEST_PARAM = '__lego';

    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param array $data
     */
    protected function validate($data)
    {
        lego_assert($data instanceof \Closure, '$data should be Closure.');
    }

    public function url(array $query = [])
    {
        return Request::fullUrlWithQuery(
            array_merge($query, [self::REQUEST_PARAM => $this->name])
        );
    }

    public static function response()
    {
        $path = Request::get(self::REQUEST_PARAM);
        if (!$path) {
            return null;
        }

        $provider = Register::get(self::class, $path);
        if (!$provider) {
            return null;
        }

        $response = $provider->data();
        if (is_null($response)) {
            return null;
        }

        return call_user_func($response, Request::all());
    }
}