<?php

namespace Lego\DataAdaptor;

use Lego\Foundation\Exceptions\LegoException;
use Lego\Foundation\FieldName;
use PhpOption\Option;

abstract class DataAdaptor
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    abstract public function getFieldValue(FieldName $fieldName): Option;

    abstract public function setFieldValue(FieldName $fieldName, $value): void;

    abstract public function save();

    public function createUniqueRule()
    {
        throw new LegoException(
            'Validation: `unique` rule only worked for Eloquent, ' .
            'you can use `validator($closure)` implement unique validation.'
        );
    }
}
