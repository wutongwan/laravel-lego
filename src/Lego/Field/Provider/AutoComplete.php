<?php namespace Lego\Field\Provider;

use Illuminate\Support\Facades\Request;
use Lego\Field\Concerns\FilterWhereEquals;
use Lego\Field\Concerns\HasSelect2Assets;
use Lego\Operator\SuggestResult;
use Lego\Register\AutoCompleteMatchHandler;

class AutoComplete extends Text
{
    use HasSelect2Assets;
    use FilterWhereEquals;

    protected function initialize()
    {
        $this->match(function ($keyword) {
            if (strlen($keyword) < $this->min) {
                return new SuggestResult([]);
            }

            return $this->query->suggest($this->name(), strval($keyword), $this->valueColumn, $this->limit);
        });
    }

    /**
     * 自动补全结果的数目
     * @var int
     */
    protected $limit = 20;

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
    protected $min;

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
    protected $remote;

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
        $this->remote = lego_register(
            AutoCompleteMatchHandler::class,
            $callable,
            md5(__CLASS__ . $this->name())
        )->remote();

        return $this;
    }

    protected $valueColumn;

    /**
     * 自动补全结果集的 key 即 存储到数据库的值
     */
    public function valueColumn($column)
    {
        $this->valueColumn = $column;

        return $this;
    }

    public function getDisplayValue()
    {
        return lego_default(
            Request::input($this->elementName() . '-text'),
            $this->store->get($this->name()),
            $this->displayValue
        );
    }

    protected function renderEditable()
    {
        $this->includeSelect2Assets();
        return $this->view('lego::default.field.auto-complete');
    }

    public function syncValueFromStore()
    {
        $associated = $this->store->getAssociated($this->name());
        if ($associated) {
            $column = $this->valueColumn ?: $associated->getKeyName();
            $this->setOriginalValue($associated->get($column));
        }
    }

    public function syncValueToStore()
    {
        $this->store->associate($this->name(), $this->getNewValue());
    }
}
