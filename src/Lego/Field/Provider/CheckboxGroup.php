<?php namespace Lego\Field\Provider;

use Collective\Html\HtmlFacade;
use Lego\Field\Concerns\FilterWhereContains;
use Lego\Foundation\Facades\LegoAssets;

class CheckboxGroup extends Select
{
    use FilterWhereContains;

    /**
     * 存储到数据库时的分隔符
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
    }

    protected function renderEditable()
    {
        LegoAssets::js('components/icheck/icheck.min.js');
        LegoAssets::css('components/icheck/skins/square/blue.css');

        return $this->view('lego::default.field.checkbox-group');
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

    protected function mutateSavingValue($value)
    {
        return join($this->separator, $value);
    }

    protected function mutateTakingValue($value)
    {
        return is_array($value) ? $value : explode($this->separator, $value);
    }
}
