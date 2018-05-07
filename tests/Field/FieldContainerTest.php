<?php namespace Lego\Tests\Field;

use Lego\Field\Provider\Text;
use Lego\Tests\TestCase;

class FieldContainerTest extends TestCase
{
    public function testFieldInitialize()
    {
        $field = new Text('txt');
        $c = $field->getContainer();
        self::assertEquals(['class' => 'form-group'], $c->getAttributes());

        $field->container('class', ['zhwei']);
        self::assertEquals(['class' => ['form-group', 'zhwei']], $c->getAttributes());

        $field->container('class', 'zhwei');
        self::assertEquals(['class' => 'zhwei'], $c->getAttributes());
    }
}
