<?php namespace Lego\Field;

use Illuminate\Support\Facades\App;
use Lego\Data\Table\EloquentTable;
use Lego\Foundation\Operators\HasMode;
use Lego\Foundation\Operators\ModeOperator;
use Lego\Foundation\Operators\MessageOperator;
use Lego\Foundation\Operators\InitializeOperator;
use Lego\Foundation\Operators\RenderStringOperator;
use Lego\Data\Row\Row;
use Lego\Data\Data;
use Lego\Data\Table\Table;

/**
 * 输入输出控件的基类
 */
abstract class Field implements HasMode
{
    use MessageOperator;
    use InitializeOperator;
    use RenderStringOperator;
    use ModeOperator; // 必须放在 `RenderStringOperator`后面

    // Plugins
    use Operators\HtmlOperator;
    use Operators\EloquentOperator;
    use Operators\ValidationOperator;
    use Operators\ValueOperator;
    use Operators\ScopeOperator;

    /**
     * 字段的唯一标记
     * @var string
     */
    private $name;

    /**
     * 字段描述
     * @var string
     */
    protected $description;

    /**
     * 对应的数据表字段名
     * @var string
     */
    protected $column;

    /**
     * 当前字段所属 Row
     *
     * @var Data|Row|Table
     */
    private $source;

    /**
     * <input type="__THIS_VALUE__" ...
     *
     * @var string
     */
    protected $inputType = 'text';

    /**
     * Field constructor.
     * @param string $name 该字段的唯一标记, 同一个控件中不能存在相同name的field
     * @param string $description 描述、标签
     * @param Data $source 对应 Row
     */
    public function __construct(string $name, string $description = null, Data $source = null)
    {
        $this->name = $name;
        $this->column = $name;
        $this->description = $description;
        $this->source = $source;

        $this->locale(App::getLocale()); // 默认使用 Laravel 的配置

        $this->triggerInitialize();
    }

    /**
     * Getter.Start
     * 下面 Getter 函数见对应的 属性注释
     */

    public function name()
    {
        return $this->name;
    }

    public function column()
    {
        return $this->column;
    }

    public function description()
    {
        return $this->description;
    }

    public function source()
    {
        return $this->source;
    }

    private $locale;

    public function locale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function isLocale($locale)
    {
        return $this->locale === $locale;
    }

    public function getInputType()
    {
        return $this->inputType;
    }

    /**
     * Filter 检索数据时, 构造此字段的查询
     * @param Table $query
     * @return Table
     */
    public function filter(Table $query): Table
    {
        return $query->whereEquals($this->column(), $this->getCurrentValue());
    }

    /**
     * Call Field's Filter
     *
     * @param Table $query
     * @return Table
     *
     * - Relation
     * - Scope
     * - Filter
     */
    public function applyFilter(Table $query)
    {
        if ($this->relation()) {
            $query->whereHas($this->relation(), function (EloquentTable $query) {
                $this->callFilterWithScope($query);
            });
        } else {
            $this->callFilterWithScope($query);
        }
        return $query;
    }

    /**
     * 数据处理逻辑
     */
    abstract public function process();

    public function getOriginalValue()
    {
        return $this->value()->original();
    }

    public function getCurrentValue()
    {
        return $this->value()->current();
    }

    /**
     * 将新的数据存储到 Source
     */
    public function syncCurrentValueToSource()
    {
        $this->source()->set($this->column(), $this->getCurrentValue());
    }

    protected function renderReadonly() : string
    {
        return view('lego::default.field.readonly', ['field' => $this]);
    }

    protected function renderDisabled() : string
    {
        return $this->renderReadonly();
    }

    protected function view($view, $data = [])
    {
        return view($view, $data)->with('field', $this);
    }
}