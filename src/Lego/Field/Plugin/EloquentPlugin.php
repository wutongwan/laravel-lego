<?php namespace Lego\Field\Plugin;

use Illuminate\Database\Eloquent\Model;
use Lego\Field\Field;

/**
 * Class EloquentPlugin
 * @package Lego\Field\Plugin
 */
trait EloquentPlugin
{
    private $relation;

    public function relation()
    {
        return $this->relation;
    }

    protected function initializeEloquentPlugin()
    {
        $names = explode('.', $this->name());
        if (count($names) > 1) {
            $this->column = last($names);
            $this->relation = join('.', array_slice($names, 0, -1));
        }
    }

    /**
     * Laravel Validation unique
     *
     * Auto except current model
     *
     * https://laravel.com/docs/master/validation#rule-unique
     */
    public function unique($id = null, $idColumn = null, $extra = null)
    {
        /** @var Field $this */
        /** @var Model $model */
        $model = $this->source()->original();

        $id = $id ?: $model->getKey() ?: 'NULL';
        $idColumn = $idColumn ?: $model->getKeyName();

        $parts = [
            "unique:{$model->getConnectionName()}.{$model->getTable()}",
            $this->column(),
            $id,
            $idColumn
        ];

        if ($extra) {
            $parts [] = trim($extra, ',');
        }

        $this->rule(join(',', $parts));

        return $this;
    }
}