<?php

namespace Lego\Register;

use Lego\Foundation\Exceptions\InvalidRegisterData;

class FieldSelfDefinedValidation extends Data
{
    /**
     * 校验注册的数据是否合法, 不合法时抛出异常.
     *
     * @param $data
     */
    protected function validate($data)
    {
        InvalidRegisterData::assert($data instanceof \Closure, '$data should be Closure.');
    }
}
