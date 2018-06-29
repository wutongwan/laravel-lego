<?php

namespace Lego\Tests\Field;

use Lego\Field\Provider\Text;
use Lego\Register\UserDefinedField;
use Lego\Tests\TestCase;

class ValueOperatorTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        lego_register(UserDefinedField::class, ExampleField::class);
    }

    public function testTakeInputValue()
    {
        $field = new Text('basic', 'Basic', []);

        $field->setOriginalValue('original');
        $this->assertEquals($field->takeInputValue(), 'original');

        $field->default('default');
        $this->assertEquals($field->takeInputValue(), 'default');

        $field->setNewValue('new');
        $this->assertEquals($field->takeInputValue(), 'new');
    }

    public function testTakeShowValue()
    {
        $field = new Text('basic', 'Basic', []);

        $field->setOriginalValue('original');
        $this->assertEquals($field->takeShowValue(), 'original');

        $field->default('default');
        $this->assertEquals($field->takeShowValue(), 'default');

        $field->setDisplayValue('display');
        $this->assertEquals($field->takeShowValue(), 'display');
    }
}
