<?php namespace Lego\Operator\Outgoing;

trait OutgoingParser
{
    /**
     * @param $data
     * @return static|bool
     */
    public static function parse($data)
    {
        if (
            $data === OutgoingInterface::class
            || $data instanceof OutgoingInterface
            || is_subclass_of($data, OutgoingInterface::class)
        ) {
            return new self($data);
        }

        return false;
    }
}
