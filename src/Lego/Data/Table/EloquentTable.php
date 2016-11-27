<?php namespace Lego\Data\Table;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Pagination\AbstractPaginator;

/**
 * Laravel ORM Data
 */
class EloquentTable extends Table
{
    /**
     * 方便补全
     * @var Eloquent|EloquentBuilder|QueryBuilder|Collection $query
     */
    protected $original;

    /**
     * 当前属性是否等于某值
     * @param $attribute
     * @param $value
     * @return static
     */
    public function whereEquals($attribute, $value)
    {
        $this->original->where($attribute, '=', $value);

        return $this;
    }

    /**
     * 当前属性大于某值
     * @param $attribute
     * @param $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     * @return static
     */
    public function whereGt($attribute, $value, bool $equals = false)
    {
        $this->original->where($attribute, $equals ? '>=' : '>', $value);

        return $this;
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
        $this->original->where($attribute, $equals ? '<=' : '<', $value);

        return $this;
    }

    /**
     * 当前属性包含特定字符串
     * @param $attribute
     * @param string $value
     * @return static
     */
    public function whereContains($attribute, string $value)
    {
        if (is_empty_string($value)) {
            return $this;
        }

        $this->original->where($attribute, 'like', '%' . trim($value, '%') . '%');

        return $this;
    }

    /**
     * 当前属性以特定字符串开头
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    public function whereStartsWith($attribute, string $value)
    {
        if (is_empty_string($value)) {
            return $this;
        }

        $this->original->where($attribute, 'like', trim($value, '%') . '%');

        return $this;
    }

    /**
     * 当前属性以特定字符串结尾
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    public function whereEndsWith($attribute, string $value)
    {
        if (is_empty_string($value)) {
            return $this;
        }

        $this->original->where($attribute, 'like', '%' . trim($value, '%'));

        return $this;
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
        if ($min && $max && $min > $max) {
            return $this->whereEquals(0, 1);
        }

        $this->original->whereBetween($attribute, [$min, $max]);

        return $this;
    }

    /**
     * 嵌套查询
     *
     * @param \Closure $closure
     * @return static
     */
    public function where(\Closure $closure)
    {
        $this->original->where(function ($query) use ($closure) {
            call_user_func($closure, lego_table($query));
        });

        return $this;
    }

    /**
     * 关联查询
     * @param $relation
     * @param \Closure $callback 由于此处 Closure 接受的参数是 Table 类，所以下面调用时封装了一次
     * @return static
     */
    public function whereHas($relation, $callback)
    {
        $this->original->whereHas(
            $relation,
            function ($query) use ($callback) {
                call_user_func($callback, lego_table($query));
            }
        );

        return $this;
    }

    public function limit($limit)
    {
        $this->original->limit($limit);

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
        $this->original->orderBy($attribute, $desc ? 'desc' : 'asc');

        return $this;
    }

    /**
     * 翻页
     * @param int $perPage
     * @param int|null $page
     * @return AbstractPaginator
     */
    protected function createPaginator(int $perPage, int $page = null) : AbstractPaginator
    {
        return $this->original->paginate($perPage, ['*'], 'page', $page);
    }
}