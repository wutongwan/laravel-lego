<?php namespace Lego\Field;

use Lego\Helper\HasMode;
use Lego\Helper\ModeHelper;
use Lego\Helper\MessageOperator;
use Lego\Helper\InitializeOperator;
use Lego\Helper\RenderStringOperator;
use Lego\Source\Row\Row;
use Lego\Source\Source;
use Lego\Source\Table\Table;

/**
 * 输入输出控件的基类
 */
abstract class Field implements HasMode
{
    use MessageOperator;
    use InitializeOperator;
    use RenderStringOperator;
    use ModeHelper; // 必须放在 `RenderStringOperator`后面

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
     * @var Source|Row|Table
     */
    private $source;

    /**
     * Field constructor.
     * @param string $name 该字段的唯一标记, 同一个控件中不能存在相同name的field
     * @param string $description 描述、标签
     * @param Source $source 对应 Row
     */
    public function __construct(string $name, string $description, Source $source = null)
    {
        $this->name = $name;
        $this->column = $name;
        $this->description = $description;
        $this->source = $source;

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
        return ($this->isRequired() ? '*' : '') . $this->description;
    }

    public function source()
    {
        return $this->source;
    }

    /**
     * Field 初始化时调用
     */
    protected function initialize()
    {
        // do nothing.
    }

    /**
     * 更新数据到 Row
     */
    public function updateValue()
    {
        $this->source->set($this->column(), $this->value()->current());
    }

    public function process()
    {
    }
}