<?php namespace Lego\Widget\Grid\Concerns;

trait CommonPipes
{
    protected function pipeTrim($value)
    {
        return trim($value);
    }

    protected function pipeStrip($value)
    {
        return strip_tags($value);
    }

    protected function pipeDate($time)
    {
        return $this->formatTime($time, 'Y-m-d');
    }

    protected function pipeDatetime($time)
    {
        return $this->formatTime($time, 'Y-m-d H:i:s');
    }

    protected function pipeTime($time)
    {
        return $this->formatTime($time, 'H:i:s');
    }

    private function formatTime($time, $format)
    {
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
