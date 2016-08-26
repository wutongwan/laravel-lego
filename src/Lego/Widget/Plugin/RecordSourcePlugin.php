<?php namespace Lego\Widget\Plugin;

use Lego\Source\Record\Record;
use Lego\Source\Source;

trait RecordSourcePlugin
{
    /**
     * @return Source|Record
     */
    protected function source() : Source
    {
        $source = parent::source();
        lego_assert($source instanceof Record, 'Forbid $source');
        return $source;
    }
}