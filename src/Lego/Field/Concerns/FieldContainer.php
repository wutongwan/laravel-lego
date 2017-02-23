<?php namespace Lego\Field\Concerns;

use Lego\Utility\HtmlUtility;

/**
 * Field 的上一层元素，Bootstrap form 中的 .form-group
 */
trait FieldContainer
{
    protected $fieldContainerAttributes = ['class' => 'form-group'];

    public function container($attributeOrAttributes, $value = null)
    {
        $attributes = !is_null($value) ? [$attributeOrAttributes => $value] : (array)$attributeOrAttributes;
        $this->fieldContainerAttributes = HtmlUtility::mergeAttributes($this->fieldContainerAttributes, $attributes);

        return $this;
    }

    public function containerAttributes()
    {
        return $this->fieldContainerAttributes;
    }
}
