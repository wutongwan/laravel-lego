<?php namespace Lego\Source\Table;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Laravel ORM Source
 */
class EloquentTable extends Table
{
    /**
     * 方便补全
     * @var Eloquent|EloquentBuilder|QueryBuilder|Collection $query
     */
    protected $query;

    /**
     * 当前属性是否等于某值
     * @param $attribute
     * @param $value
     * @return static
     */
    public function whereEquals($attribute, $value)
    {
        $this->query->where($attribute, '=', $value);

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
        $this->query->where($attribute, $equals ? '>=' : '>', $value);

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
        $this->query->where($attribute, $equals ? '<=' : '<', $value);

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
        $this->query->where($attribute, 'like', '%' . trim($value, '%') . '%');

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
        $this->query->where($attribute, 'like', trim($value, '%') . '%');

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
        $this->query->where($attribute, 'like', '%' . trim($value, '%'));

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

        $this->query->whereBetween($attribute, [$min, $max]);

        return $this;
    }

    /**
     * 关联查询
     * @param $relation
     * @param $callback
     * @return static
     */
    public function whereHas($relation, $callback)
    {
        $this->query->whereHas($relation, $callback);

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
        $this->query->orderBy($attribute, $desc ? 'desc' : 'asc');

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
        $this->query->paginate($perPage, ['*'], $pageName, $page);

        return $this;
    }

    /**
     * 处理上方所有条件后, 执行查询语句, 返回结果集
     *
     * @param array $columns 默认获取全部字段
     * @return mixed
     */
    public function fetch(array $columns = ['*'])
    {
        return $this->query->get($columns);
    }
}