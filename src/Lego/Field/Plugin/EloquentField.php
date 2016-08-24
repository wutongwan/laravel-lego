<?php namespace Lego\Field\Plugin;

use Lego\Source\EloquentSource;

/**
 * Class EloquentField
 * @package Lego\Field\Plugin
 */
trait EloquentField
{
    private function assertIsEloquentRecord()
    {
        lego_assert(
            $this->record() instanceof EloquentSource,
            'Unsupported Rule on ' . class_basename($this->record())
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

        /** @var \Eloquent $model */
        $model = $this->record()->original();

        $id = $id ?: $model->id ?: 'NULL';
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