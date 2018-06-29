<?php

namespace Lego\Field;

use Illuminate\Support\Facades\Config;
use Lego\Foundation\Concerns as FoundationConcerns;
use Lego\Operator\Query;
use Lego\Widget\Concerns\Operable;

/**
 * 输入输出控件的基类.
 */
abstract class Field implements FoundationConcerns\HasMode, \JsonSerializable
{
    use FoundationConcerns\MessageOperator,
        FoundationConcerns\InitializeOperator,
        FoundationConcerns\RenderStringOperator,
        FoundationConcerns\ModeOperator, // 必须放在 `RenderStringOperator`后面
        Operable,
        FoundationConcerns\HasHtmlAttributes,
        FoundationConcerns\HasEvents;
    use Concerns\HtmlOperator,
        Concerns\HasFieldContainer,
        Concerns\HasValidation,
        Concerns\HasValues,
        Concerns\HasScope,
        Concerns\HasLocale,
        Concerns\HasConfig,
        Concerns\FilterWhereEquals;

    /**
     * 字段的唯一标记.
     *
     * @var string
     */
    protected $name;

    /**
     * 字段描述.
     *
     * @var string
     */
    protected $description;

    /**
     * Relation Path.
     *
     * @var array
     */
    protected $relationPath;

    /**
     * 对应的数据表字段名.
     *
     * @var string
     */
    protected $column;

    /**
     * Json Path.
     *
     * @var array
     */
    protected $jsonPath;

    /**
     * 需要传递到前端的其他配置项.
     *
     * @var array
     */
    protected $extra = [];

    /**
     * Field constructor.
     *
     * @param string $name        该字段的唯一标记, 同一个控件中不能存在相同name的field
     * @param string $description 描述、标签
     * @param mixed  $data        数据域
     */
    public function __construct(string $name, string $description = null, $data = [])
    {
        $this->name = $name;
        $this->description = $description;

        list($this->relationPath, $this->column, $this->jsonPath) = FieldNameSlicer::split($name);

        $this->initializeDataOperator($data);

        $this->triggerInitialize(); // initialize traits and self.
    }

    /**
     * Getter.Start
     * 下面 Getter 函数见对应的 属性注释.
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
        return is_null($this->description)
            ? ucwords(str_replace(['.', ':'], ' ', $this->name()))
            : $this->description;
    }

    final public function applyFilter(Query $query)
    {
        if ($this->scope) {
            $this->callScope($query);
        } else {
            $this->filter($query);
        }
    }

    /**
     * 数据处理逻辑.
     */
    public function process()
    {
        $this->setAttribute([
            // html attributes
            'id'   => $this->elementId(),
            'name' => $this->elementName(),

            // lego attributes
            'lego-type'       => 'Field',
            'lego-field-type' => class_basename(static::class),
            'lego-field-mode' => $this->mode,
        ]);

        // user defined attributes
        $this->setAttribute(Config::get('lego.field.attributes', []));
    }

    /**
     * @return \Illuminate\View\View
     */
    protected function view($view, $data = [])
    {
        return view($view, $data)->with('field', $this);
    }

    public function jsonSerialize()
    {
        return [
            'element_name' => $this->elementName(),
            'element_id'   => $this->elementId(),
            'name'         => $this->name(),
            'description'  => $this->description(),
            'attributes'   => $this->getAttributes(),
            'mode'         => $this->getMode(),
            'init_value'   => $this->takeInputValue(),
            'locale'       => $this->getLocale(),
            'messages'     => $this->messages(),
            'errors'       => $this->errors(),
            'rules'        => $this->rules,
            'extra'        => $this->extra,
        ];
    }
}
