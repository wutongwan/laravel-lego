<?php

namespace Lego\Field\Provider;

class JSON extends Text
{
    protected $jsonKey;

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        lego_assert(count($this->jsonPath) > 0, 'JSON field `name` example: `array:key:sub-key:...`');
        $this->jsonKey = join('.', $this->jsonPath);
    }

    protected function mutateTakingValue($json)
    {
        return $this->decode($json) ?: $this->getDefaultValue();
    }

    protected function decode($json)
    {
        if (is_string($json)) {
            $data = json_decode($json, JSON_OBJECT_AS_ARRAY);

            return is_null($data) ? $json : $data;
        }

        return $json;
    }

    protected function encode($data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
