<?php namespace Lego\Field;

use Collective\Html\HtmlBuilder;
use Lego\Source\Record\Record;

/**
 * 输入输出控件的基类
 */
abstract class Field
{
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
     * @var Record
     */
    private $record;

    public function __construct($name, $description, Record $record)
    {
        $this->name = $name;
        $this->description = $description;
        $this->record = $record;
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

    public function record() : Record
    {
        return $this->record;
    }

    /** Getter.End */

    /**
     * Field 初始化时调用
     */
    protected function initialize()
    {
        // do nothing.
    }


    /** 数据操作.Start */

    public function getValue()
    {
        return $this->record()->get($this->name());
    }

    abstract public function getNewValue();

    /** 数据操作.End */


    /**
     * view 文件路径
     * @return string
     */
    protected function view() : string
    {
        return '';
    }

    protected function render() : string
    {
        // 1、如果实现上面的view函数, 直接渲染 view
        if ($view = $this->view()) {
            return view($view, ['field' => $this]);
        }

        // 2、渲染默认 input 控件

        return '';
    }

    /** 辅助函数 */

    public function __toString()
    {
        return $this->render();
    }
}