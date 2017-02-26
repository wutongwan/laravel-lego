<?php namespace Lego\Tests\Field;

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

    public function testTakeDefaultInputValue()
    {
        $field = new Text('basic', 'Basic', []);

        $field->setOriginalValue('original');
        $this->assertEquals($field->takeDefaultInputValue(), 'original');

        $field->default('default');
        $this->assertEquals($field->takeDefaultInputValue(), 'default');

        $field->setNewValue('new');
        $this->assertEquals($field->takeDefaultInputValue(), 'new');
    }

    public function testTakeDefaultShowValue()
    {
        $field = new Text('basic', 'Basic', []);

        $field->setOriginalValue('original');
        $this->assertEquals($field->takeDefaultShowValue(), 'original');

        $field->default('default');
        $this->assertEquals($field->takeDefaultShowValue(), 'default');

        $field->setDisplayValue('display');
        $this->assertEquals($field->takeDefaultShowValue(), 'display');
    }
}
