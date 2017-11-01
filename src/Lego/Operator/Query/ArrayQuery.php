<?php namespace Lego\Operator\Query;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Lego\Operator\Finder;
use Lego\Operator\Store\Store;

/**
 * ArrayAble
 */
class ArrayQuery extends Query
{
    public static function attempt($data)
    {
        if (is_array($data)
            || $data instanceof Collection
            || $data instanceof Arrayable
            || $data instanceof Jsonable
            || $data instanceof \JsonSerializable
            || $data instanceof \Traversable
        ) {
            return new self($data);
        }

        return false;
    }

    /**
     * @var Collection
     */
    protected $collection;

    protected function initialize()
    {
        $this->collection = new Collection($this->data);
        $this->collection->map(function ($item) {
            return Finder::store($item);
        });
    }

    public function with(array $relations)
    {
        return $this;
    }

    /**
     * 当前属性是否等于某值
     * @param $attribute
     * @param null $value
     * @return static
     */
    public function whereEquals($attribute, $value)
    {
        return $this->addFilter(function (Store $store) use ($attribute, $value) {
            return $store->get($attribute) == $value;
        });
    }

    public function whereIn($attribute, array $values)
    {
        return $this->addFilter(function (Store $store) use ($attribute, $values) {
            return in_array($store->get($attribute), $values);
        });
    }

    /**
     * 当前属性大于某值
     * @param $attribute
     * @param null $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     * @return static
     */
    public function whereGt($attribute, $value, bool $equals = false)
    {
        return $this->addFilter(
            function (Store $store) use ($attribute, $value, $equals) {
                $current = $store->get($attribute);
                return $current > $value || ($equals && $current == $value);
            }
        );
    }

    /**
     * 当前属性小于某值
     * @param $attribute
     * @param null $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     * @return static
     */
    public function whereLt($attribute, $value, bool $equals = false)
    {
        return $this->addFilter(
            function (Store $store) use ($attribute, $value, $equals) {
                $current = $store->get($attribute);
                return $current < $value || ($equals && $current == $value);
            }
        );
    }

    /**
     * 当前属性包含特定字符串
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    public function whereContains($attribute, string $value)
    {
        return $this->addFilter(function (Store $store) use ($attribute, $value) {
            return str_contains($store->get($attribute), $value);
        });
    }

    /**
     * 当前属性以特定字符串开头
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    public function whereStartsWith($attribute, string $value)
    {
        return $this->addFilter(function (Store $store) use ($attribute, $value) {
            return starts_with($store->get($attribute), $value);
        });
    }

    /**
     * 当前属性以特定字符串结尾
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    public function whereEndsWith($attribute, string $value)
    {
        return $this->addFilter(function (Store $store) use ($attribute, $value) {
            return ends_with($store->get($attribute), $value);
        });
    }

    /**
     * between, 两端开区间
     * @param $attribute
     * @param null $min
     * @param null $max
     * @return static
     */
    public function whereBetween($attribute, $min, $max)
    {
        return $this->addFilter(function (Store $store) use ($attribute, $min, $max) {
            $current = $store->get($attribute);
            if ($current instanceof \DateTime) {
                return (new Carbon($current))->between(new Carbon($min), new Carbon($max));
            }

            return $current >= $min && $current <= $max;
        });
    }

    /**
     * 嵌套查询
     *
     * @param \Closure $closure
     * @return static
     */
    public function where(\Closure $closure)
    {
        call_user_func($closure, $this);

        return $this;
    }


    /**
     * Get the relation instance for the given relation name.
     *
     * @param $name
     * @return static
     */
    public function getRelation($name)
    {
        return new self($this->collection->pluck($name));
    }

    /**
     * 关联查询
     * @param $relation
     * @param $callback
     * @return static
     */
    public function whereHas($relation, $callback)
    {
        return $this->addFilter(function (Store $store) use ($relation, $callback) {
            $related = $store->get($relation);
            if (!$related) {
                return false;
            }

            $query = new self([$related]);
            return $query->where($callback)->count() === 1;
        });
    }

    private function addFilter(\Closure $filter)
    {
        $this->collection = $this->collection->filter($filter);

        return $this;
    }

    /**
     * 限制条数
     * @param $limit
     * @return static
     */
    public function limit($limit)
    {
        $this->collection = $this->collection->slice(0, $limit);

        return $this;
    }

    /**
     * order by
     * @param $attribute
     * @param bool $desc 默认升序(false), 如需降序, 传入 true
     * @return static
     */
    public function orderBy($attribute, bool $desc = false)
    {
        $this->collection = $this->collection->sortBy($attribute, SORT_REGULAR, $desc);

        return $this;
    }

    protected function createPaginator($perPage, $columns, $pageName, $page)
    {
        return new LengthAwarePaginator($this->collection, $this->collection->count(), $perPage, $page, [
            'pageName' => $pageName,
        ]);
    }

    protected function select(array $columns)
    {
        return new Collection($this->collection->all());
    }
}
