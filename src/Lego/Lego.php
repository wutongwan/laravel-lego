<?php namespace Lego;

use Lego\Widget\Filter;
use Lego\Widget\Form;
use Lego\Widget\Grid;

/**
 * Lego 主要接口类
 */
class Lego
{
    /**
     * @param array $source
     * @return Filter
     */
    public static function filter($source)
    {
        return new Filter($source);
    }

    /**
     * @param array $source
     * @return Grid
     */
    public static function grid($source = [])
    {
        return new Grid($source);
    }

    /**
     * @param array $source
     * @return Form
     */
    public static function form($source = [])
    {
        return new Form($source);
    }
}