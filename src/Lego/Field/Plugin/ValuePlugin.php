<?php namespace Lego\Field\Plugin;

use Lego\Helper\Value;

trait ValuePlugin
{
    /**
     * @var Value
     */
    private $value;

    protected function initializeValuePlugin()
    {
        $this->value = new Value();
    }

    public function value()
    {
        return $this->value;
    }
}