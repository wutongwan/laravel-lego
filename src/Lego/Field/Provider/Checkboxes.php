<?php

namespace Lego\Field\Provider;

use Collective\Html\HtmlFacade;

class Checkboxes extends Radios
{
    protected $inputType = 'checkbox';
    protected $queryOperator = self::QUERY_CONTAINS;

    public function getInputName()
    {
        return parent::elementName() . '[]';
    }

    /**
     * 存储到数据库时的分隔符.
     *
     * @var string
     */
    protected $separator = '|';

    public function separator($glue)
    {
        $this->separator = $glue;

        return $this;
    }

    protected function initialize()
    {
        if ($separator = $this->config('separator')) {
            $this->separator = $separator;
        }
    }

    protected function renderReadonly()
    {
        $labels = array_filter(
            array_map(
                function ($value) {
                    return isset($this->options[$value]) ? $this->options[$value] : null;
                },
                $this->takeShowValue()
            )
        );

        return HtmlFacade::ul($labels);
    }

    public function isChecked($value)
    {
        return in_array($value, $this->takeInputValue());
    }

    protected function mutateSavingValue($value)
    {
        return join($this->separator, $value ?: []);
    }

    protected function mutateTakingValue($value)
    {
        return is_array($value) ? $value : explode($this->separator, $value);
    }
}
