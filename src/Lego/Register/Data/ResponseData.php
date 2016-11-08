<?php namespace Lego\Register\Data;

use Illuminate\Support\Facades\Request;
use Lego\Register\Register;

class ResponseData extends Data
{
    const REQUEST_PARAM = '__lego';

    private $response;

    private $arguments;

    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param $data
     *
     * $data 可以为数组 or Closure
     * - 数组：
     *  $data[0] 为生成 Response 的 Closure
     *  $data[1] 为供给上面 Closure 使用的参数，可以为 array 或 返回 array 的 Closure
     * - Closure：
     *  生成 Response 的 Clojure
     */
    protected function validate($data)
    {
        if (is_array($data)) {
            lego_assert($data[0] instanceof \Closure, '$data[0] should be Closure.');
            lego_assert(
                !isset($data[1]) || is_array($data[1]) || $data[1] instanceof \Closure,
                '$data[1] should be arguments of $data[0] closure.'
            );

            $this->response = $data[0];
            $this->arguments = $data[1] ?? [];
        } else {
            lego_assert($data instanceof \Closure, '$data should be Closure');
            $this->response = $data;
            $this->arguments = [];
        }
    }

    public function url(array $query = [])
    {
        return Request::fullUrlWithQuery(array_merge($query, [
            self::REQUEST_PARAM => $this->name,
        ]));
    }

    public function response()
    {
        $arguments = value($this->arguments);
        lego_assert(is_array($arguments), '$data[1] should be array or return array.');

        return call_user_func_array($this->response, $arguments);
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