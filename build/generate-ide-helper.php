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
            'add' . ucfirst(\Illuminate\Support\Str::camel($location)) . 'Button',
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
    $originalContent = file_get_contents($filename);

    $beginMark = ' * --- ide helpers begin ---';
    $begin = strpos($originalContent, $beginMark) + strlen($beginMark);
    $end = strpos($originalContent, ' * --- ide helpers end ---');

    if (!$begin || !$end) {
        dump("{$class}($filename) not found ide helper mark");
    }

    $prefix = substr($originalContent, 0, $begin);
    $suffix = substr($originalContent, $end);
    $docs = is_array($docs) ? join("\n", $docs) : $docs;

    $modified = $prefix . "\n" . $docs . "\n" . $suffix;
    file_put_contents($filename, $modified);
}

class Method
{
    public $name;
    public $return;
    public $arguments;

    public function __construct($name, $return, $arguments = null)
    {
        $this->name = $name;
        $this->return = $return;
        $this->arguments = $arguments;
    }

    public function __toString()
    {
        return " * @method \\{$this->return} {$this->name}({$this->arguments})";
    }
}
