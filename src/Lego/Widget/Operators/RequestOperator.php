<?php namespace Lego\Widget\Operatos;

use Illuminate\Support\Facades\Request;

trait RequestOperator
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