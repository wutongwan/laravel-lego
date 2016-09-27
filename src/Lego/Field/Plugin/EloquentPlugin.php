<?php namespace Lego\Field\Plugin;

use Illuminate\Database\Eloquent\Model;
use Lego\Field\Field;
use Lego\Source\Row\EloquentRow;

/**
 * Class EloquentPlugin
 * @package Lego\Field\Plugin
 */
trait EloquentPlugin
{
    private function assertIsEloquentRow()
    {
        lego_assert(
            $this->source() instanceof EloquentRow,
            'Unsupported Rule on ' . class_basename($this->source())
        );
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
        $this->assertIsEloquentRow();

        /** @var Field $this */
        /** @var Model $model */
        $model = $this->source()->data();

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