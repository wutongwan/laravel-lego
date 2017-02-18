<?php

use Lego\Register\FieldData;

class RegisterTest extends PHPUnit_Framework_TestCase
{
    public function testSetterAndGetter()
    {
        $value = time();
        lego_register(FieldData::class, $value, 'time');
        $this->assertEquals($value, \Lego\LegoRegister::get(FieldData::class, 'time'));
    }
}
