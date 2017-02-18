<?php namespace Lego\Foundation;

use Lego\Register\UserDefinedField;
use Symfony\Component\ClassLoader\ClassMapGenerator;

/**
 * call through app(Fields::class)
 */
class Fields
{
    private $fields = [];

    function __construct()
    {
        $this->addBuildInFields();

        foreach (UserDefinedField::list() as $name => $field) {
            $this->fields[$name] = $field;
        }
    }

    private function addBuildInFields()
    {
        $fields = ClassMapGenerator::createMap(__DIR__ . '/../Field/Provider');
        foreach ($fields as $field => $path) {
            $this->fields[class_basename($field)] = $field;
        }
    }

    public function get($name)
    {
        return $this->fields[$name];
    }

    public function all()
    {
        return $this->fields;
    }
}
