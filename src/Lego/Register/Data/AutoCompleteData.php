<?php namespace Lego\Register\Data;

use Illuminate\Support\Facades\Request;

class AutoCompleteData extends Data
{
    const KEYWORD_KEY = '__lego_auto_complete';

    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param $data
     */
    protected function validate($data)
    {
        lego_assert($data instanceof \Closure, '$data should be Closure.');
    }

    private $response;

    public function afterRegistered()
    {
        $this->response = lego_register(
            ResponseData::class,
            function () {
                $items = call_user_func_array($this->data(), [Request::get(self::KEYWORD_KEY), Request::all()]);
                return self::result($items);
            },
            $this->name
        );
    }

    public function response(): ResponseData
    {
        return $this->response;
    }


    /**
     * 自动补全结果的构建函数
     *
     * @param array $items [ ['id' => 1, 'text' => 'Some Text', ...], ... ]
     * @return array
     */
    private static function result(array $items)
    {
        $count = count($items);
        if ($count > 0) {
            // 简单的接口校验
            if (is_string($first = array_first($items))) {
                foreach ($items as $id => &$text) {
                    $text = ['id' => $id, 'text' => $text];
                }
                $items = array_values($items);
            }
        }

        return [
            'items' => $items,
            'total_count' => $count,
        ];
    }
}