<?php namespace Lego\Register;

use Illuminate\Support\Facades\Request;
use Lego\Foundation\Exceptions\InvalidRegisterData;
use Lego\LegoRegister;

class HighPriorityResponse extends Data
{
    const REQUEST_PARAM = '__lego';

    protected function validate($data)
    {
        InvalidRegisterData::assert($data instanceof \Closure, '$data should be Closure');
    }

    public function url(array $query = [])
    {
        if ($path = trim(Request::query(self::REQUEST_PARAM))) {
            if (!str_contains($path, $this->tag)) {
                $query[self::REQUEST_PARAM] = "{$path}+{$this->tag}";
            }
        } else {
            $query[self::REQUEST_PARAM] = $this->tag;
        }

        return Request::fullUrlWithQuery($query);
    }

    private static $tree = [];
    private static $current;

    public static function getResponse()
    {
        if (!$path = Request::get(self::REQUEST_PARAM)) {
            return null;
        }

        $path = str_replace('+', '.', $path);
        array_set(self::$tree, $path, []);
        $step = array_first(array_keys(array_get(self::$tree, self::$current)));
        if (!$data = LegoRegister::get(self::class, $step)) {
            return null;
        }

        self::$current = self::$current ? self::$current . ".{$step}" : $step;
        return call_user_func($data);
    }

    public static function exit()
    {
        return Request::fullUrlWithQuery([self::REQUEST_PARAM => null]);
    }

    public static function register($name, \Closure $closure, $appendQuery = [])
    {
        return lego_register(self::class, $closure, md5($name))->url($appendQuery);
    }
}
