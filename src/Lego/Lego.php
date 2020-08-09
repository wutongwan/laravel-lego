<?php

namespace Lego;

use Lego\Widget\Confirm;
use Lego\Widget\Filter;
use Lego\Widget\Form;
use Lego\Widget\Grid\Grid;

class Lego
{
    /**
     * Lego version.
     */
    const VERSION = '1.0';

    public static function filter($source)
    {
        return new Filter($source);
    }

    public static function grid($source = [])
    {
        return new Grid($source);
    }

    public static function form($source = [])
    {
        return new Form($source);
    }

    public static function confirm($message, callable $action, $delay = null, string $view = null)
    {
        return (new Confirm($message, $action, $delay, $view))->response();
    }

    public static function message($message, $level = 'default')
    {
        return view('lego::default.message', compact('message', 'level'));
    }
}
