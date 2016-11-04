<?php namespace Lego\Field;

use Lego\Helper\HasMode;
use Lego\Helper\ModeOperator;
use Lego\Helper\MessageOperator;
use Lego\Helper\InitializeOperator;
use Lego\Helper\RenderStringOperator;
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
    use Plugin\HtmlPlugin;
    use Plugin\EloquentPlugin;
    use Plugin\ValidationPlugin;
    use Plugin\ValuePlugin;

    /**
     * 字段的唯一标记
     * @var string
     */
    private $name;

    /**
     * 字段描述
     * @var string
     */
    private $description;

    /**
     * 对应的数据表字段名
     * @var string
     */
    private $column;

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
    public function __construct(string $name, string $description, Data $source = null)
    {
        $this->name = $name;
        $this->column = $name;
        $this->description = $description;
        $this->source = $source;

        $this->locale(\App::getLocale()); // 默认使用 Laravel 的配置

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
    abstract public function filter(Table $query): Table;

    /**
     * 数据处理逻辑
     */
    abstract public function process();
}