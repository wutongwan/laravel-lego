<?php namespace Lego\Field\Provider;

use Illuminate\Support\Facades\Request;
use Lego\Field\Field;
use Lego\Foundation\Facades\LegoAssets;
use Lego\Operator\Query\Query;
use Lego\Register\AutoCompleteMatchHandler;

class AutoComplete extends Field
{
    protected $foreignKey;

    protected function initialize()
    {
        $this->foreignKey = $this->query->getForeignKeyOfRelation($this->relation);

        $this->match(function ($keyword) {
            return $this->defaultMatch($keyword);
        });
    }

    /**
     * 默认的自动补全逻辑
     */
    private function defaultMatch($keyword)
    {
        if (!$this->relation) {
            return [];
        }

        return $this->query
            ->getRelation($this->relation)
            ->whereContains($this->column(), $keyword)
            ->limit($this->getLimit())
            ->get()
            ->pluck($this->column(), $this->getValueColumn())
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

    protected $matchIsModified;

    /**
     * 自动补全的结果集
     * @param callable $callable
     * @return $this
     */
    public function match($callable)
    {
        $this->remote = lego_register(
            AutoCompleteMatchHandler::class,
            $callable,
            md5(__CLASS__ . $this->name())
        )->remote();
        $this->matchIsModified = true;

        return $this;
    }

    private $valueColumn;

    /**
     * 自动补全结果集的 key 即 存储到数据库的值
     */
    public function valueColumn($column)
    {
        $this->valueColumn = $column;

        return $this;
    }

    public function setOriginalValue($originalValue)
    {
        return parent::setOriginalValue($this->store->get($this->foreignKey) ?: null);
    }

    public function getDisplayValue()
    {
        return lego_default(
            Request::input($this->elementName() . '-text'),
            $this->store->get($this->getColumnPathOfRelation($this->column()))
        );
    }

    protected function getValueColumn()
    {
        return lego_default($this->valueColumn, $this->store->getKeyName());
    }

    public function process()
    {
        // 以下文件仅在 editable 时加载
        if ($this->isEditable()) {
            LegoAssets::css('components/select2/dist/css/select2.min.css');
            LegoAssets::css('components/select2-bootstrap-theme/dist/select2-bootstrap.min.css');
            LegoAssets::js('components/select2/dist/js/select2.full.min.js');

            if ($this->localeIsNotEn()) {
                LegoAssets::js("components/select2/dist/js/i18n/{$this->getLocale()}.js");
            }
        }
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
        return $this->view('lego::default.field.auto-complete');
    }

    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Query $query
     * @return Query
     */
    public function filter(Query $query)
    {
        if (!$this->relation) {
            return $query->whereEquals($this->column, $this->getNewValue());
        }

        return $query->whereHas($this->relation, function (Query $query) {
            $column = $this->getValueColumn() ?: $this->column;
            return $query->whereEquals($column, $this->getNewValue());
        });
    }

    public function syncValueToStore()
    {
        if ($this->foreignKey) {
            $this->store->set($this->foreignKey, $this->getNewValue());
        }
    }
}
