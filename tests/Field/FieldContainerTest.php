<?php

namespace Lego\Tests\Field;

use Lego\Field\Provider\Text;
use Lego\Tests\TestCase;

class FieldContainerTest extends TestCase
{
    public function testFieldInitialize()
    {
        $field = new Text('txt');
        $c = $field->getContainer();
        self::assertEquals(['class' => ['lego-field-container', 'form-group']], $c->getAttributes());
        self::assertEquals(['class' => 'lego-field-container form-group'], $c->getFlattenAttributes());

        $field->container('class', ['zhwei']);
        self::assertEquals(['class' => ['lego-field-container', 'form-group', 'zhwei']], $c->getAttributes());
        self::assertEquals(['class' => 'lego-field-container form-group zhwei'], $c->getFlattenAttributes());

        $field->container('class', 'zhwei');
        self::assertEquals(['class' => 'zhwei'], $c->getAttributes());
    }
}
