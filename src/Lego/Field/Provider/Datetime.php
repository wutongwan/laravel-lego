<?php namespace Lego\Field\Provider;

use Carbon\Carbon;
use Lego\Field\Field;
use Lego\LegoAsset;

class Datetime extends Field
{
    /**
     * 日期格式，eg：Y-m-d
     * @var string
     */
    protected $format = 'Y-m-d H:i:s';

    protected $inputType = 'datetime-local';

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->rule('date');

        if (!$this->isMobile()) {
            $this->inputType = 'text';
        }
    }

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

    public function setOriginalValue($originalValue)
    {
        $this->originalValue = $this->convertToCarbon($originalValue);
    }

    public function setNewValue($value)
    {
        $this->newValue = $this->convertToCarbon($value);
    }

    protected function convertToCarbon($value)
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        return Carbon::createFromTimestamp(strtotime($value));
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
        $this->setDisplayValue($this->getShowValue());

        /**
         * 仅在 editable && 非移动端启用日期控件，移动端使用原生的输入控件
         */
        if ($this->isEditable() && !$this->isMobile()) {
            LegoAsset::css('components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
            LegoAsset::js('components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');

            if ($this->localeIsNotEn()) {
                LegoAsset::js("components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.{$this->getLocale()}.js");
            }
        }
    }

    protected function getShowValue()
    {
        /** @var Carbon $value */
        $value = $this->getNewValue();
        return $value ? $value->format($this->format) : null;
    }

    private function isMobile()
    {
        return (new \Mobile_Detect())->isMobile();
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render()
    {
        return $this->renderByMode();
    }

    protected function renderEditable()
    {
        return $this->view('lego::default.field.date');
    }
}
