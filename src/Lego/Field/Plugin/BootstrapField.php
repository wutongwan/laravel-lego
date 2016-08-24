<?php namespace Lego\Field\Plugin;

trait BootstrapField
{
    public function elementId()
    {
        return 'lego-el-id-' . $this->name();
    }

    public function elementName()
    {
        return 'lego-el-name-' . $this->name();
    }
}