<?php namespace Lego\Data\Table;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

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
        if ($min > $max) {
            return $this->whereEquals(0, 1);
        }

        $this->original->whereBetween($attribute, [$min, $max]);

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
                call_user_func($callback, lego_data($query));
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
     * @param string $pageName
     * @param int|null $page
     * @return static
     */
    public function paginate(int $perPage, string $pageName = 'page', int $page = null)
    {
        $this->original->paginate($perPage, ['*'], $pageName, $page);

        return $this;
    }

    /**
     * 处理上方所有条件后, 执行查询语句, 返回结果集
     *
     * @param array $columns 默认获取全部字段
     * @return mixed
     */
    protected function selectQuery(array $columns = []): \Illuminate\Support\Collection
    {
        return $this->original->get($columns);
    }
}