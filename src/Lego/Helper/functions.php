<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;

use Lego\LegoException;
use Lego\Register\Register;
use Lego\Source\Source;
use Lego\Source\Row\Row;
use Lego\Source\Row\EloquentRow;
use Lego\Source\Table\Table;
use Lego\Source\Table\EloquentTable;


/**
 * 根据数据类型加载 Source
 * @param $data
 * @return Source|Row|Table
 * @throws LegoException
 */
function lego_source($data)
{
    $class = is_object($data) ? get_class($data) : null;

    switch (true) {

        // Laravel Eloquent Source
        case in_array($class, [QueryBuilder::class, EloquentBuilder::class, EloquentCollection::class]):
            $source = EloquentTable::class;
            break;

        case $data instanceof Eloquent:
            $source = EloquentRow::class;
            break;

        default:
            throw new LegoException('Illegal $data type');
    }

    /** @var Source $source */
    $source = new $source;
    return $source->load($data);
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

/**
 * Alias to Register Methods
 *
 * - $data 为 null => \Lego\Register\Register::get
 * - $data 不为 null => \Lego\Register\Register::register
 *
 * @param $class
 * @param string|null $path
 * @param array $data
 * @return \Lego\Register\Data\Data
 */
function lego_register($class, $path = null, array $data = null)
{
    if (is_null($data)) {
        return Register::get($class, $path);
    }

    return Register::register($class, $path, $data);
}

if (!function_exists('class_namespace')) {
    /**
     * \Lego\Field\Text => \Lego\Field
     *
     * @param $class
     * @return string
     */
    function class_namespace($class, $appendClassName = null)
    {
        $namespace = (new \ReflectionClass($class))->getNamespaceName();

        if (!is_null($appendClassName)) {
            return $namespace . '\\' . $appendClassName;
        }

        return $namespace;
    }
}
