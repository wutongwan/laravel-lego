<?php namespace Lego\Source\Table;

use Lego\Source\Source;

abstract class Table extends Source
{
    protected $query;

    /**
     * 根据数据类型做相关的初始化, 方便进行 Query 等操作
     */
    protected function initialize()
    {
        $this->query = clone $this->data();
    }

    public function query()
    {
        return $this->query;
    }

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

    /**
     * 当前属性小于某值
     * @param $attribute
     * @param null $value
     * @param bool $equals 是否包含等于的情况, 默认不包含
     * @return static
     */
    abstract public function whereLt($attribute, $value, bool $equals = false);

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
     * 关联查询
     * @param $relation
     * @param $callback
     * @return static
     */
    abstract public function whereHas($relation, $callback);

    /**
     * order by
     * @param $attribute
     * @param bool $desc 默认升序(false), 如需降序, 传入 true
     * @return static
     */
    abstract public function orderBy($attribute, bool $desc = false);

    /**
     * 翻页
     * @param int $perPage
     * @param string $pageName
     * @param int|null $page
     * @return static
     */
    abstract public function paginate(int $perPage, string $pageName = 'page', int $page = null);

    /**
     * 处理上方所有条件后, 执行查询语句, 返回结果集
     *
     * @param array $columns 默认获取全部字段
     * @return mixed
     */
    abstract public function fetch(array $columns = ['*']);
}