<?php namespace Lego\Register\Data;

use Illuminate\Support\Facades\Request;
use Lego\Foundation\Exceptions\InvalidRegisterData;
use Lego\Register\Register;

class HighPriorityResponse extends Data
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

    public static function getResponse()
    {
        $path = Request::get(self::REQUEST_PARAM);
        if (!$path) {
            return null;
        }

        $data = Register::get(self::class, $path);
        if (!$data) {
            return null;
        }

        return call_user_func($data);
    }
}
