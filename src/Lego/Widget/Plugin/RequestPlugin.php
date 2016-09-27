<?php namespace Lego\Widget\Plugin;

use Illuminate\Support\Facades\Request;

trait RequestPlugin
{
    protected function isPost()
    {
        return Request::isMethod('post');
    }

    protected function isGet()
    {
        return Request::isMethod('get');
    }
}