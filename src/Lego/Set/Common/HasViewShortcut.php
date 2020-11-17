<?php

namespace Lego\Set\Common;

use Lego\Lego;

trait HasViewShortcut
{
    public function view($view = null, $data = [], $mergeData = [])
    {
        return Lego::view($view, $data, $mergeData);
    }
}
