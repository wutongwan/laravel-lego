<?php namespace Lego\Operator\Query;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

use ONGR\ElasticsearchDSL\Query\TermLevel\PrefixQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Sleimanx2\Plastic\DSL\SearchBuilder;

class PlasticQuery extends Query
{
    /**
     * Lego 接收到原始数据 $data 时，会顺序调用已注册 Operator 子类的此函数，
     *  当前类能处理该类型数据，则返回实例化后的 Operator ;
     *  反之 false ，返回 false 时，继续尝试下一个 Operator 子类
     *
     * @param $data
     * @return static|false
     */
    public static function attempt($data)
    {
        return $data instanceof SearchBuilder;
    }

    /**
     * @var SearchBuilder
     */
    protected $data;

    /**
     * 当前属性是否等于某值
     * @param $attribute
     * @param null $value
     * @return static
     */
    public function whereEquals($attribute, $value)
    {
        $this->data->must()->match($attribute, $value);
        return $this;
    }

    /**
     * 当前属性值是否在 values 之内
     * @param $attribute
     * @param array $values
     * @return static
     */
    public function whereIn($attribute, array $values)
    {
        $this->data->must()->nested(
            $attribute,
            function (SearchBuilder $builder) use ($attribute, $values) {
                foreach ($values as $value) {
                    $builder->filter()->match($attribute, $value);
                }
            }
        );
        return $this;
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
        $this->data->must()->range($attribute, [
            $equals ? RangeQuery::GTE : RangeQuery::GT => $value
        ]);

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
        $this->data->must()->range($attribute, [
            $equals ? RangeQuery::LTE : RangeQuery::LT => $value
        ]);

        return $this;
    }

    /**
     * 当前属性包含特定字符串
     * @param $attribute
     * @param string|null $value
     * @return static
     */
    public function whereContains($attribute, string $value)
    {
        $this->data->must()->match($attribute, $value);
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
        $this->data->must()->prefix($attribute, $value);
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
        $this->data->must()->regexp($attribute, '.*' . $value);
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
        $this->data->must()->range($attribute, [
            RangeQuery::GTE => $min,
            RangeQuery::LTE => $max,
        ]);
        return $this;
    }

    /**
     * 嵌套查询
     * @param \Closure $closure
     * @return static
     */
    public function where(\Closure $closure)
    {
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
        // TODO: Implement getRelation() method.
    }

    /**
     * 关联查询
     * @param $relation
     * @param $callback
     * @return static
     */
    public function whereHas($relation, $callback)
    {
        $this->data->nested(
            $relation,
            function ($builder) use ($callback) {
                call_user_func($callback, new self($builder));
            }
        );
        return $this;
    }

    /**
     * 限制条数
     * @param $limit
     * @return static
     */
    public function limit($limit)
    {
        $this->data->paginate($limit);
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
        $this->data->sortBy($attribute, $desc ? FieldSort::DESC : FieldSort::ASC);
        return $this;
    }

    /**
     * Create Paginator
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return AbstractPaginator
     */
    protected function createPaginator($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        // TODO: Implement createPaginator() method.
    }

    /**
     * Select from source
     *
     * @param array $columns
     * @return Collection
     */
    protected function select(array $columns)
    {
        // TODO convert to eloquent models
        return $this->data->get()->hits();
    }
}
