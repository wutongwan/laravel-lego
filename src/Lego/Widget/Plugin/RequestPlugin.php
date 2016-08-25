<?php namespace Lego\Widget\Plugin;

trait RequestPlugin
{
    protected function isPost()
    {
        return \Request::isMethod('post');
    }
}