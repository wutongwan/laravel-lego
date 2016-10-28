<?php namespace Lego\Field\Provider;

use Lego\Data\Table\Table;
use Lego\Field\Field;
use Lego\LegoAsset;
use Lego\Register\Data\ResponseData;
use Lego\Register\Register;
use Lego\Register\Data\AutoCompleteData;

class AutoComplete extends Field
{
    const KEYWORD_KEY = '__lego_auto_complete';

    protected function initialize()
    {
        // 默认自动补全列表
        $this->match(function ($arguments) {
            $keyword = array_get($arguments, self::KEYWORD_KEY);
            return [
                'items' => [['id' => 1, 'text' => $keyword]],
                'total_count' => 1,
            ];
        });
    }

    /**
     * @var integer 自动补全的最低字符数
     */
    private $minChar;

    public function minChar(int $length)
    {
        $this->minChar = $length;

        return $this;
    }

    public function getMinChar()
    {
        return $this->minChar;
    }

    /**
     * 自动补全结果的后端链接
     * @var string
     */
    private $remote;

    public function remote()
    {
        return $this->remote;
    }

    /**
     * 自动补全的结果集
     * @param callable $callable
     */
    public function match($callable)
    {
        $responsePath = $this->responsePath();

        Register::register(
            AutoCompleteData::class,
            $this->source()->original(),
            [$responsePath => $callable]
        );

        $this->remote = ResponseData::url($responsePath);
    }

    private function responsePath()
    {
        return md5(get_class($this->source()->original()) . $this->name());
    }

    public function process()
    {
        LegoAsset::css('default/select2/select2.min.css');
        LegoAsset::css('default/select2/select2-bootstrap.min.css');
        LegoAsset::js('default/select2/select2.full.min.js');

        $locale = config('app.locale');
        if ($locale !== 'en') {
            LegoAsset::js("default/select2/i18n/{$locale}.js");
        }
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render() : string
    {
        return view('lego::default.field.auto-complete', ['field' => $this]);
    }

    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     */
    public function filter(Table $query): Table
    {
        return $query->whereEquals($this->column(), $this->value()->current());
    }
}