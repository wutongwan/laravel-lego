<?php namespace Lego\Field;

use Lego\Helper\InitializeHelper;
use Lego\Helper\MessageHelper;
use Lego\Helper\StringRenderHelper;
use Lego\Source\Record\Record;
use Lego\Source\Source;
use Lego\Source\Table\Table;

/**
 * 输入输出控件的基类
 */
abstract class Field
{
    use MessageHelper;
    use InitializeHelper;
    use StringRenderHelper;

    // Plugins
    use Plugin\HtmlPlugin;
    use Plugin\RecordPlugin;
    use Plugin\EloquentPlugin;
    use Plugin\ValidationPlugin;

    const MODE_EDITABLE = 'editable';
    const MODE_DISABLED = 'disabled';
    const MODE_READONLY = 'readonly';

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
     * 当前字段所属 Record
     *
     * @var Source|Record|Table
     */
    private $source;

    /**
     * 模式, eg:editable、readonly、disabled
     */
    private $mode = self::MODE_EDITABLE;

    /**
     * value 显示前的处理器数组
     * @var \Closure[]
     */
    private $decorators = [];

    /**
     * Field constructor.
     * @param string $name 该字段的唯一标记, 同一个控件中不能存在相同name的field
     * @param string $description 描述、标签
     * @param Source $source 对应 Record
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


    public function isMode($mode)
    {
        return $this->mode === $mode;
    }

    public function isReadonly()
    {
        return $this->isMode(self::MODE_READONLY);
    }

    public function isEditable()
    {
        return $this->mode(self::MODE_EDITABLE);
    }

    public function isDisabled()
    {
        return $this->isMode(self::MODE_DISABLED);
    }

    /** Getter.End */

    /** Setter.Start */

    protected function mode($mode, $condition = true)
    {
        lego_assert(
            in_array($mode, [self::MODE_EDITABLE, self::MODE_READONLY, self::MODE_DISABLED]),
            'illegal mode'
        );

        if (value($condition)) {
            $this->mode = $mode;
        }

        return $this;
    }

    /**
     * 处理字段显示内容的装饰器
     *
     * closure 接受两个参数
     *  - 第一个参数: (第一次)为本字段的初始值, 之后为上一个decorator closure的返回值
     *  - 第二个参数: 本字段所属的数据对象
     *
     * @param \Closure $closure
     * @return $this
     */
    public function decorator(\Closure $closure)
    {
        $this->decorators[] = $closure;

        return $this;
    }

    public function readonly($condition = true)
    {
        return $this->mode(self::MODE_READONLY, $condition);
    }

    public function editable($condition = true)
    {
        return $this->mode(self::MODE_READONLY, $condition);
    }

    public function disabled($condition = true)
    {
        return $this->mode(self::MODE_DISABLED, $condition);
    }

    /** Setter.End */

    /**
     * Field 初始化时调用
     */
    protected function initialize()
    {
        // do nothing.
    }

    /** 数据操作.Start */

    public function value()
    {
        $value = $this->getOriginalValue();

        foreach ($this->decorators as $processor) {
            $value = call_user_func_array($processor, [$value, $this->source()->data()]);
        }

        return $value;
    }

    /**
     * 获取当前值
     * @return mixed
     */
    public function getOriginalValue()
    {
        if ($this->isRecordField()) {
            return $this->source()->get($this->name());
        }

        return $this->getNewValue();
    }

    /**
     * 获取修改后的值
     * @return mixed
     */
    protected function getNewValue()
    {
        return \Request::input($this->elementName());
    }

    /**
     * 更新数据到 Record
     */
    public function updateValue()
    {
        $this->source->set($this->column(), $this->getNewValue());
    }

    /** 数据操作.End */

    protected function beforeRender()
    {
    }
}