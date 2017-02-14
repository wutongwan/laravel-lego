<?php namespace Lego\Foundation\Exceptions;

/**
 * 所有内部异常请使用此类
 */
class LegoException extends \Exception
{
    public static function assert($condition, $message)
    {
        if (!$condition) {
            throw new static($message);
        }
    }
}
