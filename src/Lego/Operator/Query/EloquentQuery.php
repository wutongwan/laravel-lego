<?php namespace Lego\Operator\Query;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\AbstractPaginator;

/**
 * Laravel ORM : Eloquent
 */
class EloquentQuery extends Query
{
    public static function attempt($data)
    {
        switch (true) {
            // eg: School::class
            case is_string($data) && is_subclass_of($data, Model::class):
                /** @var Model $model */
                $model = new $data;
                return new self($model->newQuery());

            case $data instanceof Model:
                return new self($data->newQuery());

            case $data instanceof Relation:
                return new self($data->getQuery());

            // Laravel query builder
            case is_object($data)
                && in_array(get_class($data), [QueryBuilder::class, EloquentQueryBuilder::class]):
                return new self($data);

            default:
                return false;
        }
    }

    /**
     * @var Model|QueryBuilder|EloquentQueryBuilder
     */
    protected $data;

    /**
     * 当前属性是否等于某值
     * @param $attribute
     * @param $value
     * @return static
     */
    public function whereEquals($attribute, $value)
    {
        $this->data->where($attribute, '=', $value);

        return $this;
    }

    public function whereIn($attribute, array $values)
    {
        $this->data->whereIn($attribute, $values);
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
        $this->data->where($attribute, $equals ? '>=' : '>', $value);

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
        $this->data->where($attribute, $equals ? '<=' : '<', $value);

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

        $this->data->where($attribute, 'like', '%' . trim($value, '%') . '%');

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

        $this->data->where($attribute, 'like', trim($value, '%') . '%');

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

        $this->data->where($attribute, 'like', '%' . trim($value, '%'));

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
        $this->data->whereBetween($attribute, [$min, $max]);

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
        $this->data->where(function ($query) use ($closure) {
            call_user_func($closure, new self($query));
        });

        return $this;
    }

    public function getRelation($name)
    {
        return new self($this->getNestedRelation($name));
    }

    public function getForeignKeyOfRelation($name)
    {
        $relation = $this->getNestedRelation($name);
        return $relation instanceof BelongsTo
            ? trim($name, $relation->getRelation()) . $relation->getForeignKey()
            : null;
    }

    /**
     * @param string $name school.city.country
     * @return Relation|null
     */
    private function getNestedRelation($name)
    {
        $model = $this->data;
        $relation = null;
        foreach (explode('.', $name) as $relation) {
            /** @var Relation $relation */
            $relation = $model->newQuery()->getRelation($relation);
            if ($relation) {
                $model = $relation->getRelated();
            }
        }
        return $relation;
    }

    /**
     * 关联查询
     * @param $relation
     * @param \Closure $callback 由于此处 Closure 接受的参数是 Table 类，所以下面调用时封装了一次
     * @return static
     */
    public function whereHas($relation, $callback)
    {
        $this->data->whereHas(
            $relation,
            function ($query) use ($callback) {
                call_user_func($callback, new self($query));
            }
        );

        return $this;
    }

    public function limit($limit)
    {
        $this->data->limit($limit);

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
        $this->data->orderBy($attribute, $desc ? 'desc' : 'asc');

        return $this;
    }

    /**
     * 翻页
     * @param int $perPage
     * @param int|null $page
     * @return AbstractPaginator
     */
    protected function createPaginator($perPage = null, $columns = null, $pageName = null, $page = null)
    {
        return $this->data->paginate($perPage, ['*'], 'page', $page);
    }

    protected function select(array $columns)
    {
        return $this->data->get($columns);
    }
}
