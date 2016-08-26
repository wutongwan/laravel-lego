<?php namespace Lego\Field\Plugin;

use Lego\Field\Field;
use Lego\Source\Table\EloquentTable;

/**
 * Class EloquentPlugin
 * @package Lego\Field\Plugin
 */
trait EloquentPlugin
{
    private function assertIsEloquentRecord()
    {
        lego_assert(
            $this->source() instanceof EloquentTable,
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
        $this->assertIsEloquentRecord();

        /** @var Field $this */
        /** @var \Eloquent $model */
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