<?php

namespace Lego\Operator\Plastic;

use Illuminate\Support\Collection;
use Lego\Operator\Query;
use Lego\Operator\SuggestResult;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Sleimanx2\Plastic\DSL\SearchBuilder;
use Sleimanx2\Plastic\PlasticPaginator;
use Sleimanx2\Plastic\PlasticResult;

class PlasticQuery extends Query
{
    /**
     * Lego 接收到原始数据 $data 时，会顺序调用已注册 Operator 子类的此函数，
     *  当前类能处理该类型数据，则返回实例化后的 Operator ;
     *  反之 false ，返回 false 时，继续尝试下一个 Operator 子类.
     *
     * @param SearchBuilder $data
     *
     * @return static|false
     */
    public static function parse($data)
    {
        return $data instanceof SearchBuilder ? new static($data) : false;
    }

    /**
     * @var SearchBuilder
     */
    protected $data;

    protected $with = [];

    /**
     * Query with eager loading.
     *
     * @param array $relations
     *
     * @return static
     */
    public function with(array $relations)
    {
        $this->with = array_unique(array_merge($this->with, $relations));

        return $this;
    }

    /**
     * 当前属性是否等于某值
     *
     * @param $attribute
     * @param null $value
     *
     * @return static
     */
    public function whereEquals($attribute, $value)
    {
        $this->data->must()->term($attribute, $value);

        return $this;
    }

    /**
     * 当前属性值是否在 values 之内.
     *
     * @param $attribute
     * @param array $values
     *
     * @return static
     */
    public function whereIn($attribute, array $values)
    {
        $this->data->must()->terms($attribute, $values);

        return $this;
    }

    /**
     * 当前属性大于某值
     *
     * @param $attribute
     * @param null $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     *
     * @return static
     */
    public function whereGt($attribute, $value, bool $equals = false)
    {
        $this->data->must()->range($attribute, [
            $equals ? RangeQuery::GTE : RangeQuery::GT => $value,
        ]);

        return $this;
    }

    /**
     * 当前属性小于某值
     *
     * @param $attribute
     * @param null $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     *
     * @return static
     */
    public function whereLt($attribute, $value, bool $equals = false)
    {
        $this->data->must()->range($attribute, [
            $equals ? RangeQuery::LTE : RangeQuery::LT => $value,
        ]);

        return $this;
    }

    /**
     * 当前属性包含特定字符串.
     *
     * @param $attribute
     * @param string|null $value
     *
     * @return static
     */
    public function whereContains($attribute, string $value)
    {
        $this->data->must()->match($attribute, $value);

        return $this;
    }

    /**
     * 当前属性以特定字符串开头.
     *
     * @param $attribute
     * @param string|null $value
     *
     * @return static
     */
    public function whereStartsWith($attribute, string $value)
    {
        $this->data->must()->prefix($attribute, $value);

        return $this;
    }

    /**
     * 当前属性以特定字符串结尾.
     *
     * @param $attribute
     * @param string|null $value
     *
     * @return static
     */
    public function whereEndsWith($attribute, string $value)
    {
        $this->data->must()->regexp($attribute, '.*' . $value);

        return $this;
    }

    /**
     * between, 两端开区间.
     *
     * @param $attribute
     * @param null $min
     * @param null $max
     *
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

    protected $limit;

    /**
     * 限制条数.
     *
     * @param $limit
     *
     * @return static
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * order by.
     *
     * @param $attribute
     * @param bool $desc 默认升序(false), 如需降序, 传入 true
     *
     * @return static
     */
    public function orderBy($attribute, bool $desc = false)
    {
        $this->data->sortBy($attribute, $desc ? FieldSort::DESC : FieldSort::ASC);

        return $this;
    }

    protected function createLengthAwarePaginator($perPage, $columns, $pageName, $page)
    {
        $perPage = $perPage ?: $this->limit;
        $from = $perPage * ($page - 1);
        $size = $perPage;

        $this->data->from($from)->size($size);

        /** @var PlasticResult $result */
        $result = $this->performSelect($columns);

        return new PlasticPaginator($result, $size, $page);
    }

    protected function createLengthNotAwarePaginator($perPage, $columns, $pageName, $page)
    {
        return $this->createLengthAwarePaginator($perPage, $columns, $pageName, $page);
    }

    /**
     * Select from source.
     *
     * @param array $columns
     *
     * @return Collection
     */
    protected function select(array $columns)
    {
        return $this->performSelect($columns)->hits();
    }

    protected function performSelect(array $columns)
    {
        $filler = new PlasticEloquentFiller();
        $filler->select($columns)->with($this->with);

        $this->data->setModelFiller($filler);

        return $this->data->get();
    }

    /**
     * Query Scope.
     */
    public function whereScope($scope, $value)
    {
    }

    public function suggest(
        $attribute,
        string $keyword,
        string $valueColumn = null,
        int $limit = 20
    ): SuggestResult {
        return new SuggestResult([]);
    }
}
