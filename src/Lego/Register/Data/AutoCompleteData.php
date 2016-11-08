<?php namespace Lego\Register\Data;

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
            [
                $this->data(),
                function () {
                    return [\Request::get(self::KEYWORD_KEY), \Request::all()];
                }
            ],
            $this->name
        );
    }

    public function response(): ResponseData
    {
        return $this->response;
    }
}