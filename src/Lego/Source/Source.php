<?php namespace Lego\Source;

/**
 * Lego 数据源 接口
 */
interface Source
{
    public function __construct($data);

    public function set($key, $value);

    public function get($key, $default = null);
}