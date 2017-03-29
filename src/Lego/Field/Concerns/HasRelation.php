<?php namespace Lego\Field\Concerns;

use Lego\Operator\Query\Query;

/**
 * Field 中 Relation 的处理逻辑
 *
 * Relation example: school.city.name
 *
 * @mixin \Lego\Field\Field
 */
trait HasRelation
{
    /**
     * eg: city_id
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * eg: school.city
     *
     * @var string
     */
    protected $relation;

    /**
     * eg: name
     *
     * @var string
     */
    protected $relationColumn;

    protected function initializeHasRelation()
    {
        if (strpos($this->name, '.') === false) {
            return;
        }

        $parts = explode('.', $this->name);
        $this->relation = join('.', array_slice($parts, 0, -1));
        $this->relationColumn = last($parts);
        $this->foreignKey = $this->query->getForeignKeyOfRelation($this->relation);
    }

    public function relation()
    {
        return $this->relation;
    }

    public function foreignKey()
    {
        return $this->foreignKey;
    }

    public function getColumnPathOfRelation($column)
    {
        return $this->relation ? "{$this->relation}.{$column}" : $column;
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
