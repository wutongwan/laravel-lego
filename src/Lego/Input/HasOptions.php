<?php

namespace Lego\Input;

trait HasOptions
{
    /**
     * @var array
     */
    private $options;

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function options(array $options)
    {
        $this->options = $options;
        return $this;
    }

    abstract public function isMultiSelect(): bool;

    /**
     * @param array $values
     * @return $this
     */
    public function optionValues(array $values)
    {
        return $this->options(array_combine($values, $values));
    }

    public function getSelected(): array
    {
        $value = $this->values()->getCurrentValue();
        if ($value === null) {
            return [];
        }
        if (is_string($value)) {
            return [$value];
        }
        return (array)$value;
    }

    private function renderOptions(array $options, string $selected, string $label = '', int $level = 0)
    {
        $html = [];
        foreach ($options as $value => $text) {
            if (is_iterable($text)) {
                $html[] = self::renderOptions($text, $selected, $value, $level + 1);
            } else {
                $html[] = sprintf('<option value="%s" %s>%s</option>', $value, $selected === $value ? 'selected' : '', $text);
            }
        }
        return $level === 0
            ? join('', $html)
            : sprintf('<optgroup label="%s">%s</optgroup>', $label, join('', $html));
    }

    public function getOptionsHtml(): string
    {
        return $this->renderOptions($this->getOptions(), (string)$this->values()->getCurrentValue());
    }
}
