<?php namespace Lego\Operator\Query;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

use Lego\Foundation\Exceptions\LegoException;
use ONGR\ElasticsearchDSL\Query\TermLevel\PrefixQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Sleimanx2\Plastic\DSL\SearchBuilder;
use Sleimanx2\Plastic\PlasticPaginator;

class PlasticQuery extends Query
{
    /**
     * Lego 接收到原始数据 $data 时，会顺序调用已注册 Operator 子类的此函数，
     *  当前类能处理该类型数据，则返回实例化后的 Operator ;
     *  反之 false ，返回 false 时，继续尝试下一个 Operator 子类
     *
     * @param SearchBuilder $data
     * @return static|false
     */
    public static function attempt($data)
    {
        return $data instanceof SearchBuilder ? new static($data) : false;
    }

    /**
     * @var SearchBuilder
     */
    protected $data;

    /**
     * @var Model
     */
    protected $model;

    protected function initialize()
    {
        /**
         * TODO 耍流氓的办法，等整个调通一起去提 PR
         */
        $rft = new \ReflectionClass(SearchBuilder::class);
        $property = $rft->getProperty('model');
        $property->setAccessible(true);
        $this->model = $property->getValue($this->data);
    }

    /**
     * 当前属性是否等于某值
     * @param $attribute
     * @param null $value
     * @return static
     */
    public function whereEquals($attribute, $value)
    {
        $this->data->must()->term($attribute, $value);
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
        $this->data->must()->terms($attribute, $values);
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
        call_user_func($closure, new self($this->data->must()));

        return $this;
    }

    /**
     * Get the relation instance for the given relation name.
     */
    public function getRelation($name)
    {
        throw new LegoException(__METHOD__ . ' is not defined yet.');
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

    protected $limit;

    /**
     * 限制条数
     * @param $limit
     * @return static
     */
    public function limit($limit)
    {
        $this->limit = $limit;
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
    protected function createPaginator($perPage, $columns, $pageName, $page)
    {
        $perPage = $perPage ?: $this->limit;

        $from = $perPage * ($page - 1);
        $size = $perPage;

        $result = $this->from($from)->size($size)->get();

        return new PlasticPaginator($result, $size, $page);
    }

    /**
     * Select from source
     *
     * @param array $columns
     * @return Collection
     */
    protected function select(array $columns)
    {
        $this->data->size($this->limit);
        $ids = $this->data->get()->hits()->pluck('id')->toArray();
        return $this->model->findMany($ids, $columns);
    }
}
