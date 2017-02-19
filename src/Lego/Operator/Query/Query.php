<?php namespace Lego\Operator\Query;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Pagination\AbstractPaginator;
use Lego\Operator\Finder;
use Lego\Operator\Operator;
use Lego\Operator\Store\Store;
use Traversable;

/**
 * Query 类为 Lego 提供统一的 query API
 */
abstract class Query extends Operator implements \ArrayAccess, Arrayable, \Countable, \IteratorAggregate, Jsonable, \JsonSerializable
{

    /**
     * @var AbstractPaginator
     */
    protected $paginator;

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

    public function whereGte($attribute, $value)
    {
        return $this->whereGt($attribute, $value, true);
    }

    /**
     * 当前属性小于某值
     * @param $attribute
     * @param null $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     * @return static
     */
    abstract public function whereLt($attribute, $value, bool $equals = false);

    public function whereLte($attribute, $value)
    {
        return $this->whereLt($attribute, $value, true);
    }

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
     * 嵌套查询
     * @param \Closure $closure
     * @return static
     */
    abstract public function where(\Closure $closure);

    /**
     * Get the relation instance for the given relation name.
     *
     * @param $name
     * @return static
     */
    abstract public function getRelation($name);

    /**
     * 关联查询
     * @param $relation
     * @param $callback
     * @return static
     */
    abstract public function whereHas($relation, $callback);

    /**
     * 限制条数
     * @param $limit
     * @return static
     */
    abstract public function limit($limit);

    /**
     * order by
     * @param $attribute
     * @param bool $desc 默认升序(false), 如需降序, 传入 true
     * @return static
     */
    abstract public function orderBy($attribute, bool $desc = false);

    /**
     * Create Paginator
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return AbstractPaginator
     */
    abstract protected function createPaginator($perPage = null, $columns = ['*'], $pageName = 'page', $page = null);

    /**
     * Paginator API
     *
     * @param null $perPage
     * @param array $columns
     * @param null $pageName
     * @param null $page
     * @return AbstractPaginator|Store[]
     */
    public function paginate($perPage = null, $columns = null, $pageName = null, $page = null)
    {
        $perPage = is_null($perPage) ? config('lego.paginator.per-page') : $perPage;
        $pageName = is_null($pageName) ? config('lego.paginator.page-name') : $pageName;
        $columns = is_null($columns) ? ['*'] : $columns;

        $this->paginator = $this->createPaginator($perPage, $columns, $pageName, $page);
        $this->paginator->setCollection(
            $this->paginator->getCollection()->map(function ($row) {
                return Finder::store($row);
            })
        );

        return $this->paginator;
    }

    public function paginator()
    {
        if (!$this->paginator) {
            $this->paginate();
        }

        return $this->paginator;
    }

    public function get()
    {
        return $this->paginator();
    }

    /** Array Access Interface Methods. */

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->paginator()->toArray();
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
        return $this->paginator()->getIterator();
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
        return $this->paginator()->offsetExists($offset);
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
        return $this->paginator()->offsetGet($offset);
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
        $this->paginator()->offsetSet($offset, $value);
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
        $this->paginator()->offsetUnset($offset);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->paginator()->toJson(
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
        return $this->paginator()->count();
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
        return $this->paginator()->jsonSerialize();
    }
}
