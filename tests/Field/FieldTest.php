<?php

namespace Field;

use Lego\Foundation\Facades\LegoFields;
use Lego\Register\UserDefinedField;
use Lego\Tests\Field\ExampleField;
use Lego\Tests\TestCase;
use Lego\Widget\Form;

class FieldTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        lego_register(UserDefinedField::class, ExampleField::class);
    }

    public function testExampleDefined()
    {
        $fields = LegoFields::all();
        $this->assertArrayHasKey(class_basename(ExampleField::class), $fields);
        $this->assertTrue(in_array(ExampleField::class, $fields));
    }

    public function testFormAdd()
    {
        $form = new Form([]);
        /** @var ExampleField $field */
        $field = $form->addExampleField('example', 'Example Field');

        $this->assertEquals($field->name(), 'example');
        $this->assertEquals($field->description(), 'Example Field');
        $this->assertInstanceOf(ExampleField::class, $field);
    }
}
