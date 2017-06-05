<?php

include __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->register(\Lego\LegoServiceProvider::class);

use Lego\Foundation\Button;
use Lego\Foundation\Facades\LegoFields;
use Lego\Widget\Concerns\HasFields;
use Lego\Widget\Filter;
use Lego\Widget\Form;
use Lego\Widget\Grid\Grid;
use Lego\Widget\Widget;

// add field helper
$methods = [];
foreach (LegoFields::all() as $field) {
    $methods[] = new Method(
        'add' . class_basename($field),
        $field,
        'string $fieldName, $fieldDescription = null'
    );
}
insertDocsToClass(HasFields::class, $methods);

// add button helper
foreach ([Filter::class, Form::class, Grid::class] as $widget) {
    $methods = [];
    /** @var Widget $instance */
    $instance = new $widget([]);
    foreach ($instance->buttonLocations() as $location) {
        $methods[] = new Method(
            'add' . ucfirst(camel_case($location)) . 'Button',
            Button::class,
            '$text, $url = null, $id = null'
        );
    }
    insertDocsToClass($widget, $methods);
}

echo "IDE helpers generated.\n";

function insertDocsToClass($class, $docs)
{
    $filename = (new \ReflectionClass($class))->getFileName();
    $docs = is_array($docs) ? join("\n", $docs) : $docs;
    file_put_contents($filename, str_replace(' * @lego-ide-helper', $docs, file_get_contents($filename)));
}


class Method
{
    public $name;
    public $return;
    public $arguments;

    function __construct($name, $return, $arguments = null)
    {
        $this->name = $name;
        $this->return = $return;
        $this->arguments = $arguments;
    }

    function __toString()
    {
        return " * @method \\{$this->return} {$this->name}({$this->arguments})";
    }
}
