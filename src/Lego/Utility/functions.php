<?php

use Lego\LegoException;
use Lego\Register\Data\ResponseData;
use Lego\Register\Register;
use Lego\Data\Row\Row;
use Lego\Data\Table\Table;

/**
 * 将传入的数据转换为 Row
 *
 * @param $data
 * @return Row
 * @throws LegoException
 */
function lego_row($data)
{
    switch (true) {
        case $data instanceof Row:
            return $data;

        case is_array($data):
            $source = \Lego\Data\Row\ArrayRow::class;
            break;

        case $data instanceof \Illuminate\Database\Eloquent\Model:
            $source = \Lego\Data\Row\EloquentRow::class;
            break;

        case is_object($data):
            $source = \Lego\Data\Row\ObjectRow::class;
            break;

        default:
            throw new LegoException('Illegal $data type');
    }

    /** @var Row $source */
    $source = new $source;
    return $source->load($data);
}

/**
 * 将数据转换为 Table
 * @param $data
 * @return Table
 * @throws LegoException
 */
function lego_table($data)
{
    $class = is_object($data) ? get_class($data) : null;

    switch (true) {
        case $data instanceof Table:
            return $data;

        // Laravel Eloquent Data
        case in_array($class, [
            Illuminate\Database\Eloquent\Model::class,
            Illuminate\Database\Query\Builder::class,
            Illuminate\Database\Eloquent\Builder::class,
            Illuminate\Database\Eloquent\Collection::class,
        ]):
            $source = \Lego\Data\Table\EloquentTable::class;
            break;

        case is_array($data):
        case $data instanceof \Illuminate\Support\Collection:
            $source = \Lego\Data\Table\ArrayTable::class;
            break;

        case is_object($data):
            $source = \Lego\Data\Table\ObjectTable::class;
            break;

        default:
            throw new LegoException('Illegal $data type');
    }

    /** @var Table $source */
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
 * @param $name
 * @param mixed $data
 * @param string|null $dataName
 * @return \Lego\Register\Data\Data
 */
function lego_register($name, $data = null, $dataName = null)
{
    return Register::register($name, $data, $dataName);
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

function is_empty_string($string)
{
    return strlen(trim($string)) === 0;
}

/**
 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
 */
function lego_response()
{
    /**
     * Check registered global response
     */
    $registeredResponse = ResponseData::getResponse();
    if (!is_null($registeredResponse)) {
        return $registeredResponse;
    }

    return call_user_func_array('response', func_get_args());
}