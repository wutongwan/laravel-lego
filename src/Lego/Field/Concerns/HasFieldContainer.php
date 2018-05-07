<?php namespace Lego\Field\Concerns;

use Lego\Field\FieldContainer;

/**
 * Field 的上一层元素，Bootstrap form 中的 .form-group
 */
trait HasFieldContainer
{
    /**
     * @var FieldContainer
     */
    protected $container;

    protected function initializeHasFieldContainer()
    {
        $this->container = new FieldContainer();
        $this->container->setAttribute('class', 'form-group');
    }

    public function container($attributeOrAttributes, $value = null)
    {
        $this->container->setAttribute($attributeOrAttributes, $value);

        return $this;
    }

    public function containerAttributes()
    {
        return $this->getContainer()->getAttributes();
    }

    public function getContainer()
    {
        return $this->container;
    }
}
