<?php namespace Lego\Data\Table;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Lego\Data\Data;
use Traversable;

abstract class Table extends Data implements \ArrayAccess, Arrayable, \Countable, \IteratorAggregate, Jsonable, \JsonSerializable
{
    protected $query;

    /** @var Collection $rows */
    private $rows;

    /**
     * 根据数据类型做相关的初始化, 方便进行 Query 等操作
     */
    protected function initialize()
    {
        $this->query = clone $this->original();
    }

    /**
     * 当前属性是否等于某值
     * @param $attribute
     * @param null $value
     * @return static
     */
    abstract public function whereEquals($attribute, $value);

    /**
     * 当前属性大于某值
     * @param $attribute
     * @param null $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     * @return static
     */
    abstract public function whereGt($attribute, $value, bool $equals = false);

    /**
     * 当前属性小于某值
     * @param $attribute
     * @param null $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     * @return static
     */
    abstract public function whereLt($attribute, $value, bool $equals = false);

    /**
     * 当前属性包含特定字符串
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    abstract public function whereContains($attribute, string $value);

    /**
     * 当前属性以特定字符串开头
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    abstract public function whereStartsWith($attribute, string $value);

    /**
     * 当前属性以特定字符串结尾
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    abstract public function whereEndsWith($attribute, string $value);

    /**
     * between, 两端开区间
     * @param $attribute
     * @param null $min
     * @param null $max
     * @return static
     */
    abstract public function whereBetween($attribute, $min, $max);

    /**
     * 关联查询
     * @param $relation
     * @param $callback
     * @return static
     */
    abstract public function whereHas($relation, $callback);

    /**
     * order by
     * @param $attribute
     * @param bool $desc 默认升序(false), 如需降序, 传入 true
     * @return static
     */
    abstract public function orderBy($attribute, bool $desc = false);

    /**
     * 翻页
     * @param int $perPage
     * @param string $pageName
     * @param int|null $page
     * @return static
     */
    abstract public function paginate(int $perPage, string $pageName = 'page', int $page = null);

    /**
     * 处理上方所有条件后, 执行查询语句, 返回结果集
     *
     * @param array $columns 默认获取全部字段
     * @return Collection
     */
    abstract protected function selectQuery(array $columns = []): Collection;

    /**
     * 对外提供发起检索的函数
     *
     * @param array $columns
     * @return Collection
     */
    final public function fetch(array $columns = ['*'])
    {
        $this->rows = $this->selectQuery($columns);

        return $this->rows;
    }

    public function rows(): Collection
    {
        if (is_null($this->rows)) {
            $this->fetch();
        }

        return $this->rows;
    }

    /** Array Access Interface Methods. */

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->rows()->toArray();
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return $this->rows()->getIterator();
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->rows()->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->rows()->offsetGet($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->rows()->offsetSet($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->rows()->offsetUnset($offset);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->rows()->toJson(
            $options === 0 ? JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE : $options
        );
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return $this->rows()->count();
    }

    /**
     * Specify original which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed original which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->rows()->jsonSerialize();
    }
}