<?php

namespace Lego\Set\Common;

use Lego\Contracts\Input\HiddenInput;
use Lego\Input\Input;

abstract class InputWrapper
{
    /**
     * @var Input
     */
    protected $input;

    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    /**
     * @return Input
     */
    public function getInput(): Input
    {
        return $this->input;
    }

    public function isHiddenInput(): bool
    {
        return $this->input instanceof HiddenInput;
    }

    public function __call($method, $parameters)
    {
        // forward calls to $input
        if (method_exists($this->input, $method)) {
            $result = call_user_func_array([$this->input, $method], $parameters);
            return $result === $this->input ? $this : $result; // 根据返回值判定是否返回 $this
        }

        throw new \BadMethodCallException($method);
    }
}
