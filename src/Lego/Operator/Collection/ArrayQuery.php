<?php namespace Lego\Operator\Collection;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lego\Operator\Finder;
use Lego\Operator\Query;
use Lego\Operator\SuggestResult;
use Lego\Operator\Store;

/**
 * ArrayAble
 */
class ArrayQuery extends Query
{
    public static function parse($data)
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
        $this->collection = $this->collection->map(function ($item) {
            return Finder::createStore($item);
        });
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
            return Str::contains($store->get($attribute), $value);
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
            return Str::startsWith($store->get($attribute), $value);
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
            return Str::endsWith($store->get($attribute), $value);
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

    protected function addFilter(\Closure $filter)
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

    protected function createLengthAwarePaginator($perPage, $columns, $pageName, $page)
    {
        return new LengthAwarePaginator($this->collection, $this->collection->count(), $perPage, $page, [
            'pageName' => $pageName,
        ]);
    }

    protected function createLengthNotAwarePaginator($perPage, $columns, $pageName, $page)
    {
        return new Paginator($this->collection, $perPage, $page, [
            'pageName' => $pageName,
        ]);
    }

    protected function select(array $columns)
    {
        return $this->collection;
    }

    public function suggest($attribute, string $keyword, string $valueColumn = null, int $limit = 20): SuggestResult
    {
        $items = (new Collection($this->data))
            ->filter(function ($item) use ($attribute, $keyword) {
                return Str::contains(data_get($item, $attribute), $keyword);
            })
            ->take($limit)
            ->pluck($attribute, $valueColumn ?: $attribute)
            ->toArray();

        return new SuggestResult($items);
    }

    public function whereScope($scope, $value)
    {
        return $this;
    }
}
