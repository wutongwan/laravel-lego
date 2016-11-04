<?php namespace Lego\Field\Provider;

use Lego\Field\Field;
use Lego\Data\Table\Table;
use Lego\LegoAsset;
use Lego\Register\Register;
use Lego\Register\Data\ResponseData;
use Lego\Register\Data\AutoCompleteData;

class AutoComplete extends Field
{

    protected function initialize()
    {
        // 默认自动补全列表
        $this->match(function ($keyword) {
            return self::result($this->defaultMatch($keyword));
        });
    }

    /**
     * 默认的自动补全逻辑
     *
     * @param $keyword
     * @return array
     */
    private function defaultMatch($keyword)
    {
        if (is_empty_string($keyword)) {
            return [];
        }

        if (!$related = $this->related()) {
            return [];
        }

        return $related
            ->where($this->relationColumn(), 'like', '%' . trim($keyword) . '%')
            ->limit($this->getLimit())
            ->pluck($this->relationColumn(), $related->getKeyName())
            ->map(function ($column, $id) {
                return ['text' => $column, 'id' => $id];
            })
            ->values()
            ->all();
    }

    /**
     * 自动补全结果的数目
     * @var int
     */
    private $limit = 20;

    public function limit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @var integer 触发自动补全的最低字符数
     */
    private $min;

    public function min(int $length)
    {
        $this->min = $length;

        return $this;
    }

    public function getMin()
    {
        return $this->min;
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
     * @return $this
     */
    public function match($callable)
    {
        $hash = md5(get_class($this->source()->original()) . $this->name());

        /** @var AutoCompleteData $data */
        $data = lego_register(AutoCompleteData::class, $callable, $hash);
        $this->remote = $data->response()->url();

        return $this;
    }

    public function process()
    {
        $current = $this->value()->current();
        $related = $this->related();
        if ($current && $related) {
            $model = $related->where($related->getKeyName(), $current)->first([$this->relationColumn()]);
            if ($model) {
                $this->value()->setShow($model->{$this->relationColumn()});
            }
        }

        // 以下文件仅在 editable 时加载
        if ($this->isEditable()) {
            LegoAsset::css('default/select2/select2.min.css');
            LegoAsset::css('default/select2/select2-bootstrap.min.css');
            LegoAsset::js('default/select2/select2.full.min.js');

            if (!$this->isLocale('en')) {
                LegoAsset::js("default/select2/i18n/" . $this->getLocale() . ".js");
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
        return view('lego::default.field.auto-complete', ['field' => $this]);
    }

    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     */
    public function filter(Table $query): Table
    {
        return $query->whereEquals(
            $query->original()->getModel()->getKeyName(),
            $this->value()->current()
        );
    }

    /**
     * 自动补全结果的构建函数
     *
     * @param array $items [ ['id' => 1, 'text' => 'Some Text', ...], ... ]
     * @return array
     */
    public static function result(array $items)
    {
        $count = count($items);
        if ($count > 0) {
            // 简单的接口校验
            $first = $items[0];
            lego_assert(
                array_key_exists('id', $first) && array_key_exists('text', $first),
                'AutoComplete items illegal.'
            );
        }

        return [
            'items' => $items,
            'total_count' => $count,
        ];
    }
}