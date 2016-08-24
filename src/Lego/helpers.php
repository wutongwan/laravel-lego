<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;

use Lego\LegoException;
use Lego\Source\Record\EloquentRecord;
use Lego\Source\Record\Record;
use Lego\Source\Source;
use Lego\Source\EloquentSource;


/**
 * 根据数据类型加载 Source
 * @param $data
 * @return Source
 * @throws LegoException
 */
function lego_source($data)
{
    $first = isset($data[0]) ? $data[0] : [];
    $class = is_object($data) ? get_class($data) : null;

    switch ($data) {

        // Laravel Eloquent Source
        case in_array($class, [QueryBuilder::class, EloquentBuilder::class, EloquentCollection::class]):
        case $first instanceof Eloquent:
            $source = EloquentSource::class;
            break;

        default:
            throw new LegoException('Illegal $data type');
    }

    /** @var Source $source */
    $source = new $source;
    return $source->load($data);
}

/**
 * 根据数据类型加载 Record
 * @param $data
 * @return Record
 * @throws LegoException
 */
function lego_record($data)
{
    switch ($data) {
        case $data instanceof Eloquent:
            $record = EloquentRecord::class;
            break;

        default:
            throw new LegoException('Illegal $data type');
    }

    /** @var Record $record */
    $record = new $record;
    return $record->load($data);
}

/**
 * Lego Assert
 * @param $condition
 * @param $description
 * @throws LegoException
 */
function lego_assert($condition, $description)
{
    if (!$condition) {
        throw new LegoException($description);
    }
}
