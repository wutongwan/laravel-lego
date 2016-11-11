<?php namespace Lego;

use Lego\Widget\Filter;
use Lego\Widget\Form;
use Lego\Widget\Grid;

class Lego
{
    /**
     * Lego version.
     */
    const VERSION = '0.1.3';

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