<?php

namespace Lego\Field\Provider;

use Illuminate\Support\Facades\Request;
use Lego\Operator\Query;
use Lego\Operator\SuggestResult;
use Lego\Register\AutoCompleteMatchHandler;

class AutoComplete extends Text
{
    protected $labelElementSuffix = '-text';

    protected function initialize()
    {
        $this->match(function ($keyword) {
            if (strlen($keyword) < $this->min) {
                return new SuggestResult([]);
            }

            return $this->query->suggest($this->name(), strval($keyword), $this->valueColumn, $this->limit);
        });
    }

    public function getLabelElementSuffix()
    {
        return $this->labelElementSuffix;
    }

    public function setLabelElementSuffix($name)
    {
        $this->labelElementSuffix = $name;

        return $this;
    }

    /**
     * 自动补全结果的数目.
     *
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
     * @var int 触发自动补全的最低字符数
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
     * 自动补全结果的后端链接.
     *
     * @var string
     */
    protected $remote;

    public function remote()
    {
        return $this->remote;
    }

    /**
     * 自动补全的结果集.
     *
     * @param callable $callable
     *
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

    public function takeShowValue()
    {
        return $this->mutateTakingValue(
            lego_default(
                $this->getDisplayValue(),
                // 区别只有下面这一行，用于从 Filter 筛选提交后提取对应的文本
                Request::input($this->elementName() . $this->getLabelElementSuffix()),
                $this->getDefaultValue(),
                $this->getOriginalValue()
            )
        );
    }

    protected function renderEditable()
    {
        return $this->view('lego::default.field.auto-complete');
    }

    public function syncValueFromStore()
    {
        if ($this->relationPath) {
            $associated = $this->store->getAssociated($this->name());
            if ($associated) {
                $column = $this->valueColumn ?: $associated->getKeyName();
                $this->setOriginalValue($associated->get($column));

                // set default display value
                if (!$this->getDisplayValue()) {
                    $this->setDisplayValue($associated->get($this->column()));
                }
            }
        } else {
            $this->setOriginalValue(
                $this->store->get($this->name())
            );
        }
    }

    public function syncValueToStore()
    {
        if ($this->relationPath) {
            $this->store->associate($this->name(), $this->getNewValue());
        } else {
            $this->store->set($this->name(), $this->getNewValue());
        }
    }

    public function filter(Query $query)
    {
        if (!$this->relationPath) {
            $column = $this->valueColumn ?: $this->column();

            return $query->whereEquals($column, $this->getNewValue());
        }

        return $query->whereEquals($this->name(), $this->getNewValue());
    }
}
