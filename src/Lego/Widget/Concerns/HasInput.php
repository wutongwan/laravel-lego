<?php

namespace Lego\Widget\Concerns;

use Illuminate\Support\Facades\Input;

trait HasInput
{
    private function getDefaultInput()
    {
        return Input::all();
    }

    protected $userCustomInput = [];

    protected function getInput($key = null, $default = null)
    {
        $array = $this->userCustomInput ?: $this->getDefaultInput();

        return $key ? $array[$key] ?? $default : $array;
    }

    public function withInput(array $input)
    {
        $this->userCustomInput = $input;

        return $this;
    }
}
