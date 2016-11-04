<?php namespace Lego\Register\Data;

class AutoCompleteData extends Data
{
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
        $this->response = lego_register(ResponseData::class, $this->data(), $this->name);
    }

    public function response() : ResponseData
    {
        return $this->response;
    }
}