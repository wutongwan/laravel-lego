<?php namespace Lego\Widget\Grid;

use Illuminate\Support\HtmlString;
use Lego\Foundation\Exceptions\LegoException;
use Lego\Operator\Finder;
use Lego\Operator\Store\Store;

class Cell
{
    use Concerns\CommonPipes;

    private $name;
    private $description;

    /**
     * @var PipeHandler[]
     */
    private $pipes = [];

    private $data;
    /**
     * @var Store
     */
    private $store;
    private $default;

    function __construct($name, $description)
    {
        $pipes = explode('|', $name);
        $this->name = $pipes[0];
        $this->description = $description;

        foreach (array_slice($pipes, 1) as $pipe) {
            $this->pipe($pipe);
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function description()
    {
        return $this->description;
    }

    public function default($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * 修正函数，对现有值进行一定的修正
     *
     * $callable 可以接受两个参数
     *  - 当前值
     *  - 当前值所属的对象（ Model ）
     *
     * @param callable|string $pipe
     * @return $this
     * @throws LegoException
     */
    public function pipe($pipe)
    {
        $this->pipes[] = new PipeHandler($pipe, array_slice(func_get_args(), 1));

        return $this;
    }

    public function cell($callable)
    {
        return $this->pipe($callable);
    }

    public function copy()
    {
        return clone $this;
    }

    public function fill($data): self
    {
        if ($data instanceof Store) {
            $this->data = $data->getOriginalData();
            $this->store = $data;
        } else {
            $this->data = $data;
            $this->store = Finder::store($data);
        }

        return $this;
    }

    public function store()
    {
        return $this->store;
    }

    /**
     * cell original value before pipes is called
     *
     * @return string
     */
    public function getOriginalValue()
    {
        return $this->store->get($this->name);
    }

    public function getDefaultValue()
    {
        return $this->default;
    }

    /**
     * cell value after pipes processed
     *
     * @return HtmlString
     */
    public function value()
    {
        $value = lego_default($this->getOriginalValue(), $this->default);
        foreach ($this->pipes as $pipe) {
            $value = $pipe->handle($value, $this->data, $this);
        }
        return new HtmlString((string)$value);
    }

    /**
     * cell plain value after pipes processed
     *
     * @return string
     */
    public function getPlainValue()
    {
        return strip_tags($this->value()->toHtml());
    }

    public function __toString()
    {
        return $this->value()->toHtml();
    }
}
