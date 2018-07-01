<?php

namespace Lego\Tests\Field;

use Lego\Field\Provider\Text;
use Lego\Tests\TestCase;

class HtmlOperatorTest extends TestCase
{
    public function testAttr()
    {
        $field = new Text('text');
        $field->attr('class', 'example');
        $field->process();
        $attributes = $field->getFlattenAttributes();
        self::assertSame($attributes['class'], 'example form-control');
    }
}
