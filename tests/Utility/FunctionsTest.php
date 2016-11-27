<?php

class FunctionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * 取出参数类的命名空间
     *
     * @dataProvider classNamespaceProvider
     */
    public function testClassNamespace($class, $namespace, $append = null, $afterAppend = null)
    {
        $this->assertEquals($namespace, class_namespace($class));
    }

    public function classNamespaceProvider()
    {
        return [
            [\Illuminate\Database\Eloquent\Collection::class, 'Illuminate\Database\Eloquent'],
            [\Lego\Field\Field::class, 'Lego\Field'],
            [Closure::class, ''],
            [\Lego\Field\Provider\Text::class, 'Lego\Field\Provider', 'Text', \Lego\Field\Provider\Text::class],
        ];
    }
}