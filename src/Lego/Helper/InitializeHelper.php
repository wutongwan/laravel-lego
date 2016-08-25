<?php namespace Lego\Helper;

trait InitializeHelper
{
    /**
     * 初始化操作, 在类构造函数中调用
     */
    abstract protected function initialize();
}