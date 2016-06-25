<?php namespace Lego\Source;

/**
 * Lego 数据源 接口
 */
abstract class Source
{
    abstract public function __construct($data);
}