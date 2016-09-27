<?php namespace Lego;

use Lego\Widget\Filter;
use Lego\Widget\Form;
use Lego\Widget\Grid;

/**
 * Lego 主要接口类
 */
class Lego
{
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
}