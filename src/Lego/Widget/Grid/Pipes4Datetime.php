<?php namespace Lego\Widget\Grid;

class Pipes4Datetime extends Pipes
{
    public function handleDate()
    {
        return $this->handleDateFormat('Y-m-d');
    }

    public function handleDatetime()
    {
        return $this->handleDateFormat('Y-m-d H:i:s');
    }

    public function handleTime()
    {
        return $this->handleDateFormat('H:i:s');
    }

    public function handleDateFormat($format)
    {
        $time = $this->value();

        if (is_int($time)) {
            return date($format, $time);
        }

        if (is_string($time)) {
            return date($format, strtotime($time));
        }

        if ($time instanceof \DateTime) {
            return $time->format($format);
        }

        return null;
    }
}
