<?php namespace Lego\Field\Provider;

use Collective\Html\HtmlFacade;
use Lego\Field\Concerns\FilterWhereContains;
use Lego\Field\Concerns\HasOptions;
use Lego\Foundation\Facades\LegoAssets;

class Checkboxes extends Text
{
    use FilterWhereContains;
    use HasOptions;

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
        if ($separator = $this->config('separator')) {
            $this->separator = $separator;
        }
    }

    protected function renderEditable()
    {
        LegoAssets::js('components/icheck/icheck.min.js');
        LegoAssets::css('components/icheck/skins/square/blue.css');

        return $this->view('lego::default.field.checkboxes');
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
