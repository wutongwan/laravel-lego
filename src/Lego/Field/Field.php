<?php namespace Lego\Field;

use Lego\Foundation\Concerns\HasMode;
use Lego\Foundation\Concerns\ModeOperator;
use Lego\Foundation\Concerns\MessageOperator;
use Lego\Foundation\Concerns\InitializeOperator;
use Lego\Foundation\Concerns\RenderStringOperator;
use Lego\Operator\Finder;
use Lego\Operator\Query\Query;
use Lego\Operator\Store\Store;
use Lego\Widget\Concerns\Operable;

/**
 * 输入输出控件的基类
 */
abstract class Field implements HasMode
{
    use MessageOperator,
        InitializeOperator,
        RenderStringOperator,
        ModeOperator, // 必须放在 `RenderStringOperator`后面
        Operable;

    use Concerns\HtmlOperator,
        Concerns\FieldContainer,
        Concerns\ValidationOperator,
        Concerns\ValueOperator,
        Concerns\ScopeOperator,
        Concerns\HasRelation,
        Concerns\HasLocale,
        Concerns\FilterWhereEquals;

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
     * 原始数据
     * @var mixed|null
     */
    protected $data;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var Store
     */
    public $store;

    /**
     * Field constructor.
     * @param string $name 该字段的唯一标记, 同一个控件中不能存在相同name的field
     * @param string $description 描述、标签
     * @param mixed $data 数据域
     */
    public function __construct(string $name, string $description = null, $data = [])
    {
        $this->name = $name;

        /**
         * Example
         *  - name : school.city.name
         *  - column : name
         *  - relation : school.city
         *  - description <default> : School City Name
         */
        $parts = explode('.', $name);
        $this->column = last($parts);
        $this->description = $description ?: ucwords(join(' ', $parts));

        if ($data instanceof Store) {
            $this->data = $data->getOriginalData();
            $this->store = $data;
        } else {
            $this->data = $data;
            $this->store = Finder::store($data);
        }
        $this->query = Finder::query($this->data);

        $this->triggerInitialize(); // initialize traits and self.
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

    final public function applyFilter(Query $query)
    {
        if ($this->scope) {
            $this->callScope($query);
        } else {
            $this->filter($query);
        }
    }


    /**
     * 数据处理逻辑
     */
    abstract public function process();

    /**
     * 将新的数据存储到 Store
     */
    public function syncValueToStore()
    {
        $this->store->set($this->getColumnPathOfRelation($this->column), $this->getNewValue());
    }

    protected function view($view, $data = [])
    {
        return view($view, $data)->with('field', $this);
    }
}
