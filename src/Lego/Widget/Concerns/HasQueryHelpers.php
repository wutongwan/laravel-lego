<?php namespace Lego\Widget\Concerns;

trait HasQueryHelpers
{
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->query->with($relations);
        return $this;
    }

    public function limit($limit)
    {
        $this->query->limit($limit);

        return $this;
    }

    public function orderBy($column, bool $desc = false)
    {
        $this->query->orderBy($column, $desc);

        return $this;
    }

    public function orderByDesc($column)
    {
        return $this->orderBy($column, true);
    }
}
