<?php

use Lego\Register\Data;
use Lego\Register\FieldData;

class RegisterTest extends PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $this->assertEquals([], lego_register('field.data', [], 'empty')->data());
    }

    public function testRegister()
    {
        $data = lego_register('field.data', ['a' => 'b'], 'test');
        $this->assertData($data);
    }

    private function assertData(Data $data)
    {
        $this->assertTrue($data instanceof FieldData);
        $this->assertEquals($data->data()['a'], 'b');
    }
}
