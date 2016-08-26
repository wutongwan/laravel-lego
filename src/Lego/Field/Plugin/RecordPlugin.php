<?php namespace Lego\Field\Plugin;

use Lego\Source\Record\Record;

trait RecordPlugin
{
    /**
     * 判断当前 Field 操作的源数据是否 Record
     * @return bool
     */
    protected function isRecordField()
    {
        return $this->source() instanceof Record;
    }
}