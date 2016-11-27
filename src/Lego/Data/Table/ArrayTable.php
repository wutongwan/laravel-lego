<?php namespace Lego\Data\Table;

use Carbon\Carbon;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Lego\Data\Row\Row;

class ArrayTable extends Table
{
    protected function initialize()
    {
        $this->rows = collect($this->original())->map('lego_row');
    }

    /**
     * 当前属性是否等于某值
     * @param $attribute
     * @param null $value
     * @return static
     */
    public function whereEquals($attribute, $value)
    {
        return $this->addFilterToRows(function (Row $row) use ($attribute, $value) {
           return $row->get($attribute) == $value;
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
        return $this->addFilterToRows(
            function (Row $row) use ($attribute, $value, $equals) {
                $current = $row->get($attribute);
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
        return $this->addFilterToRows(
            function (Row $row) use ($attribute, $value, $equals) {
                $current = $row->get($attribute);
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
        return $this->addFilterToRows(function (Row $row) use ($attribute, $value) {
            return str_contains($row->get($attribute), $value);
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
        return $this->addFilterToRows(function (Row $row) use ($attribute, $value) {
            return starts_with($row->get($attribute), $value);
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
        return $this->addFilterToRows(function (Row $row) use ($attribute, $value) {
            return ends_with($row->get($attribute), $value);
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
        return $this->addFilterToRows(function (Row $row) use ($attribute, $min, $max) {
            $current = $row->get($attribute);
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
     * 关联查询
     * @param $relation
     * @param $callback
     * @return static
     */
    public function whereHas($relation, $callback)
    {
        return $this->addFilterToRows(function (Row $row) use ($relation, $callback) {
            if (!$related = $row->get($relation)) {
                return false;
            }
            return lego_table([$related])->where($callback)->count() > 0;
        });
    }

    private function addFilterToRows(\Closure $filter)
    {
        $this->rows = $this->rows->filter($filter);

        return $this;
    }

    /**
     * 限制条数
     * @param $limit
     * @return static
     */
    public function limit($limit)
    {
        $this->rows->slice(0, $limit);

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
        $this->rows->sortBy($attribute, SORT_REGULAR, $desc);

        return $this;
    }

    /**
     * 翻页
     * @param int $perPage
     * @param int|null $page
     * @return AbstractPaginator
     */
    protected function createPaginator(int $perPage, int $page = null): AbstractPaginator
    {
        return new LengthAwarePaginator($this->rows, count($this->rows), $perPage);
    }
}