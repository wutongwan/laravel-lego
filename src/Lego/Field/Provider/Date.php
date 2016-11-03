<?php namespace Lego\Field\Provider;

use Carbon\Carbon;
use Lego\Data\Table\Table;
use Lego\Field\Field;
use Lego\LegoAsset;

class Date extends Field
{
    /**
     * Filter 中日期的筛选框是否是范围输入
     *
     * @var bool
     */
    private $range = false;

    public function range()
    {
        $this->range = true;

        return $this;
    }

    public function isRange()
    {
        return $this->range;
    }

    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     */
    public function filter(Table $query): Table
    {
        $value = $this->value()->current();
        if ($this->range && is_array($value)) {
            $min = $value['min'];
            $max = $value['max'];
            lego_assert($min instanceof Carbon && $max instanceof Carbon, 'illegal value');
            return $query->whereBetween($this->column(), $min, $max);
        } else {
            lego_assert($value instanceof Carbon, 'illegal value');
            return $query->whereEquals($this->column(), $value);
        }
    }

    /**
     * 日期格式，eg：Y-m-d
     * @var string
     */
    private $format;

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getJavaScriptFormat()
    {
        return str_replace(['d', 'm', 'Y'], ['dd', 'mm', 'yyyy'], $this->format);
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->rule('date');
        $this->format('Y-m-d');
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
        if ($this->isEditable()) {
            LegoAsset::css('default/datepicker/bootstrap-datepicker3.standalone.min.css');
            LegoAsset::js('default/datepicker/bootstrap-datepicker.min.js');

            if (!$this->isLocale('en')) {
                LegoAsset::js('default/datepicker/i18n/bootstrap-datepicker.zh-CN.min.js');
            }
        }
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render(): string
    {
        return $this->renderByMode();
    }

    protected function renderEditable(): string
    {
        return view('lego::default.field.date', ['field' => $this]);
    }
}