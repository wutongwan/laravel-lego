<?php

namespace Lego\Field\Provider;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Lego\Field\Field;
use Lego\Foundation\Facades\LegoAssets;

class Datetime extends Field
{
    const DATETIME_LOCAL = 'datetime-local';

    /**
     * 日期格式，eg：Y-m-d.
     *
     * @var string
     */
    protected $format = 'Y-m-d H:i:s';

    protected $inputType = self::DATETIME_LOCAL;

    /**
     * User Agent Detector.
     *
     * @var \Mobile_Detect
     */
    protected $detector;

    /**
     * 是否禁用原生日期控件.
     *
     * @var bool
     */
    protected $disableNativePicker = false;

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->rule('date');

        $this->detector = App::make(\Mobile_Detect::class);

        $this->disableNativePicker(
            $this->inputType === self::DATETIME_LOCAL || $this->config('disable-native-picker')
        );
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

    /**
     * 当前 format 的 JS format.
     *
     * @return string
     */
    public function getJavaScriptFormat()
    {
        return str_replace(
            ['d', 'm', 'Y', 'H', 'i', 's', 'a', 'A', 'g', 'G'],
            ['dd', 'mm', 'yyyy', 'hh', 'ii', 'ss', 'p', 'P', 'H', 'h'],
            $this->format
        );
    }

    /**
     * Datetime Picker Options.
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

    /**
     * Bootstrap DatetimePicker 的配置项.
     *
     * @return array
     */
    public function getPickerOptions()
    {
        return [
            'format'               => $this->getJavaScriptFormat(),
            'language'             => $this->getLocale(),
            'startView'            => $this->startView,
            'minView'              => $this->minView,
            'maxView'              => $this->maxView,
            'todayBtn'             => 'linked',
            'todayHighlight'       => true,
            'autoclose'            => true,
            'disableTouchKeyboard' => true,
            'ignoreReadonly'       => true,
        ];
    }

    protected function mutateTakingValue($datetime)
    {
        return $this->formatDatetimeString($datetime);
    }

    protected function mutateSavingValue($datetime)
    {
        return $this->formatDatetimeString($datetime);
    }

    /**
     * 将传入的 datetime 使用 strtotime 解析，然后转为指定格式.
     *
     * @param $datetime
     * @param null $format
     *
     * @return null|string
     */
    protected function formatDatetimeString($datetime, $format = null)
    {
        $format = $format ?: $this->format;

        if (!$datetime) {
            return null;
        }

        if ($datetime instanceof Carbon) {
            return $datetime->format($format);
        }

        return date($format, strtotime($datetime));
    }

    /**
     * 禁止当前 Field 使用原生日期控件.
     *
     * @param bool $condition
     *
     * @return $this
     */
    public function disableNativePicker($condition = true)
    {
        $this->disableNativePicker = (bool) $condition;

        return $this;
    }

    /**
     * 当前是否启用了原生日期控件.
     *
     * @return bool
     */
    public function nativePickerIsEnabled()
    {
        return (!$this->disableNativePicker) && $this->detector->isMobile();
    }

    /**
     * 数据处理逻辑.
     */
    public function process()
    {
        parent::process();

        /*
         * 仅在 editable && 非移动端启用日期控件，移动端使用原生的输入控件
         */
        if ($this->isEditable() && !$this->nativePickerIsEnabled()) {
            $this->inputType = 'text';

            $prefix = 'components/smalot-bootstrap-datetimepicker';
            LegoAssets::css($prefix . '/css/bootstrap-datetimepicker.min.css');
            LegoAssets::js($prefix . '/js/bootstrap-datetimepicker.min.js');

            if ($this->localeIsNotEn()) {
                LegoAssets::js($prefix . "/js/locales/bootstrap-datetimepicker.{$this->getLocale()}.js");
            }
        }
    }

    /**
     * 渲染当前对象
     *
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

    /**
     * 获取 Carbon 类型的当前输入值
     *
     * @return Carbon
     */
    public function getCarbonNewValue()
    {
        return new Carbon($this->getNewValue());
    }
}
