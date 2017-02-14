<?php namespace Lego\Register\Data;

use Illuminate\Support\Facades\Request;
use Lego\Foundation\Exceptions\InvalidRegisterData;
use Lego\Register\Register;

class ResponseData extends Data
{
    const REQUEST_PARAM = '__lego';

    protected function validate($data)
    {
        InvalidRegisterData::assert($data instanceof \Closure, '$data should be Closure');
    }

    public function url(array $query = [])
    {
        $query[self::REQUEST_PARAM] = $this->tag;

        return Request::fullUrlWithQuery($query);
    }

    public function response()
    {
        return call_user_func($this->data);
    }

    public static function getResponse()
    {
        $path = Request::get(self::REQUEST_PARAM);
        if (!$path) {
            return null;
        }

        /** @var self $data */
        $data = Register::get(self::class, $path);
        if (!$data) {
            return null;
        }

        return $data->response();
    }
}
