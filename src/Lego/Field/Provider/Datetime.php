<?php namespace Lego\Field\Provider;

use Carbon\Carbon;
use Lego\Data\Table\Table;
use Lego\Field\Field;
use Lego\LegoAsset;

class Datetime extends Field
{
    protected $inputType = 'datetime-local';

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->rule('date');
        $this->format('Y-m-d H:i:s');

        if (!$this->isMobile()) {
            $this->inputType = 'text';
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
            return $query->whereBetween($this->column(), $min, $max);
        } else {
            return $query->whereEquals($this->column(), $value);
        }
    }

    /**
     * Datetime Picker Options
     *
     * doc: https://github.com/smalot/bootstrap-datetimepicker#options
     *
     * 参考链接：
     * How i can avoid time selection: https://github.com/smalot/bootstrap-datetimepicker/issues/581
     * Using as Time Picker Only: https://github.com/smalot/bootstrap-datetimepicker/issues/569
     */
    protected $minView = 'hour';

    protected $maxView = 'decade';

    protected $startView = 'month';

    public function getPickerOptions()
    {
        return [
            'format' => $this->getJavaScriptFormat(),
            'language' => $this->getLocale(),
            'startView' => $this->startView,
            'minView' => $this->minView,
            'maxView' => $this->maxView,
            'todayBtn' => "linked",
            'todayHighlight' => true,
            'autoclose' => true,
            'disableTouchKeyboard' => true,
        ];
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
        // 将值转换为 Carbon
        $this->value()->decorator(function ($value) {
            if (is_array($value)) {
                foreach ($value as &$item) {
                    $item = new Carbon(date($this->format, strtotime($item)));
                }
            } else {
                return new Carbon(date($this->format, strtotime($value)));
            }
            return $value;
        });

        if ($this->isEditable() && !$this->isMobile()) {
            LegoAsset::css('default/datetimepicker/bootstrap-datetimepicker.min.css');
            LegoAsset::js('default/datetimepicker/bootstrap-datetimepicker.min.js');

            if (!$this->isLocale('en')) {
                LegoAsset::js('default/datetimepicker/i18n/bootstrap-datetimepicker.zh-CN.js');
            }
        }
    }

    private function isMobile()
    {
        return (new \Mobile_Detect())->isMobile();
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