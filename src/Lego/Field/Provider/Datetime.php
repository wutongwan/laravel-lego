<?php namespace Lego\Field\Provider;

use Carbon\Carbon;
use Lego\Field\Field;
use Lego\Foundation\Facades\LegoAssets;

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

    protected function autoFormat($datetime)
    {
        if (!$datetime) {
            return null;
        }

        if ($datetime instanceof Carbon) {
            return $datetime->format($this->format);
        }

        return date($this->format, strtotime($datetime));
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
        /**
         * 仅在 editable && 非移动端启用日期控件，移动端使用原生的输入控件
         */
        if ($this->isEditable() && !$this->isMobile()) {
            LegoAssets::css('components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
            LegoAssets::js('components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');

            if ($this->localeIsNotEn()) {
                LegoAssets::js("components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.{$this->getLocale()}.js");
            }
        }
    }

    public function takeDefaultInputValue()
    {
        return $this->autoFormat(parent::takeDefaultInputValue());
    }

    public function takeDefaultShowValue()
    {
        return $this->autoFormat(parent::takeDefaultShowValue());
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
