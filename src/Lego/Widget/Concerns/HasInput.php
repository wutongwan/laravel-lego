<?php

namespace Lego\Widget\Concerns;

use Illuminate\Support\Facades\Request;

trait HasInput
{
    private function getDefaultInput()
    {
        return Request::all();
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
