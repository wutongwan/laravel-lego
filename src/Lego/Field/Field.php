<?php namespace Lego\Field;

use Illuminate\Support\MessageBag;

use Lego\Field\Plugin\BootstrapField;
use Lego\Field\Plugin\EloquentField;
use Lego\Source\Record\Record;

/**
 * 输入输出控件的基类
 */
abstract class Field
{
    use EloquentField;
    use BootstrapField;

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
     * @var Record
     */
    private $record;

    /**
     * 改 Field 所有 Validation
     * eg: ['required', 'email']
     */
    private $rules = [];

    /**
     * 所有提示信息
     * @var MessageBag
     */
    private $messages;

    /**
     * 所有错误信息
     * @var MessageBag
     */
    private $errors;

    /**
     * 模式, eg:editable、readonly、disabled
     */
    private $mode;

    /**
     * value 显示前的处理器数组
     * @var \Closure[]
     */
    private $decorators = [];

    /**
     * Placeholder
     * @var string
     */
    private $placeholder;

    /**
     * Html element attributes.
     * @var array
     */
    private $attributes = [];

    /**
     * Field constructor.
     * @param string $name 该字段的唯一标记, 同一个控件中不能存在相同name的field
     * @param string $description 描述、标签
     * @param Record $record 对应 Record
     */
    public function __construct(string $name, string $description, Record $record)
    {
        $this->name = $name;
        $this->description = $description;
        $this->record = $record;

        // 初始化相关属性
        $this->messages = new MessageBag();
        $this->errors = new MessageBag();
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

    public function rules() : array
    {
        return $this->rules;
    }

    public function messages() : MessageBag
    {
        return $this->messages;
    }

    public function errors() : MessageBag
    {
        return $this->errors;
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

    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    /** Getter.End */

    /** Setter.Start */

    public function rule($rule, $condition = true)
    {
        if (value($condition) && !in_array($rule, $this->rules)) {
            $this->rules [] = $rule;
        }
        return $this;
    }

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

    public function placeholder($placeholder = null)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * 设置此字段为必填
     *
     * @param bool $condition
     * @return Field
     */
    public function required($condition = true)
    {
        return $this->rule('required', $condition);
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

    /**
     * 设置 Field 的 html 属性
     *
     * 第一个参数类型可以为字符串或数组
     *      - 数组, 将merge到现有attributes中
     *      - 字符串, 和对应的 value 放入 attributes
     *
     * @param array|string $attributesOrAttributes
     * @param string|null $value
     * @return $this
     */
    public function attr($attributesOrAttributes, $value = null)
    {
        if (is_array($attributesOrAttributes)) {
            $this->attributes = array_merge($this->attributes, $attributesOrAttributes);
            return $this;
        }

        $this->attributes [$attributesOrAttributes] = $value;
        return $this;
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
            $value = call_user_func_array($processor, [$value, $this->record()->original()]);
        }

        return $value;
    }

    /**
     * 获取当前值
     * @return mixed
     */
    public function getOriginalValue()
    {
        return $this->record()->get($this->name());
    }

    /**
     * 获取修改后的值
     * @return mixed
     */
    abstract protected function getNewValue();

    /**
     * 验证当前值是否符合 rules
     *
     * 验证失败时, 报错信息会写到 $this->errors
     *
     * @return bool 是否通过验证
     */
    public function validate()
    {
        $validator = \Validator::make(
            [$this->column => $this->getNewValue()],
            [$this->column => $this->rules()],
            [],
            [$this->column => $this->description]
        );

        if ($validator->fails()) {
            $this->errors()->merge($validator->messages());
            return false;
        }

        return true;
    }

    /**
     * 更新数据到 Record
     */
    public function updateValue()
    {
        $this->record->set($this->column(), $this->getNewValue());
    }

    /** 数据操作.End */

    protected function inputType()
    {
        return 'text';
    }

    /**
     * view 文件路径
     * @return string
     */
    protected function view() : string
    {
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