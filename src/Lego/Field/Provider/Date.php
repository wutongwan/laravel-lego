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
            $min = new Carbon($value['min']);
            $max = new Carbon($value['max']);
            return $query->whereBetween($this->column(), $min, $max);
        } else {
            return $query->whereEquals($this->column(), new Carbon($value));
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
        return str_replace(
            ['d', 'm', 'Y', 'H', 'i', 's', 'a', 'A', 'g', 'G'],
            ['dd', 'mm', 'yyyy', 'hh', 'ii', 'ss', 'p', 'P', 'H', 'h'],
            $this->format
        );
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
            LegoAsset::css('default/datetimepicker/bootstrap-datetimepicker.min.css');
            LegoAsset::js('default/datetimepicker/bootstrap-datetimepicker.min.js');

            if (!$this->isLocale('en')) {
                LegoAsset::js('default/datetimepicker/i18n/bootstrap-datetimepicker.zh-CN.js');
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
        if ($this->range) {
            return view('lego::default.field.date-range', ['field' => $this]);
        }
        return view('lego::default.field.date', ['field' => $this]);
    }
}