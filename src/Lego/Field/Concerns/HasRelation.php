<?php namespace Lego\Field\Concerns;

use Lego\Operator\Query\Query;

/**
 * Field 中 Relation 的处理逻辑
 *
 * Relation example: school.city.name
 */
trait HasRelation
{
    /**
     * eg: school.city
     *
     * @var string
     */
    protected $relation;

    protected function initializeHasRelation()
    {
        // Relationship
        $this->relation = join('.', array_slice(explode('.', $this->name), 0, -1));
    }

    public function relation()
    {
        return $this->relation;
    }

    public function getColumnPathOfRelation($column)
    {
        return join('.', [$this->relation, $column]);
    }

    /**
     * 嵌套查询的辅助函数
     *
     * @param Query $query
     * @param \Closure $closure
     * @return Query
     */
    protected function filterWithRelation(Query $query, \Closure $closure)
    {
        if ($this->relation) {
            return $query->whereHas($this->relation, $closure);
        }

        return call_user_func($closure, $query);
    }
}
