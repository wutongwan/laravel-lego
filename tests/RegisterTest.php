<?php

use Lego\Register\Data\Data;
use Lego\Register\Data\FieldData;

class RegisterTest extends PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $this->assertEquals([], lego_register('field.data', 'empty'));
    }

    public function testRegister()
    {
        $data = lego_register('field.data', 'test', ['a' => 'b']);
        $this->assertData($data);

        $data = lego_register('field.data', 'test');
        $this->assertData($data);
    }

    private function assertData(Data $data)
    {
        $this->assertTrue($data instanceof FieldData);
        $this->assertEquals($data->data()['a'], 'b');
        $this->assertEquals($data->data('a'), 'b');
        $this->assertEquals($data->data('hello', 'default'), 'default');
    }
}