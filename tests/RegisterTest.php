<?php namespace Lego\Tests;

use Lego\Register\FieldData;

class RegisterTest extends \PHPUnit\Framework\TestCase
{
    public function testSetterAndGetter()
    {
        $value = time();
        lego_register(FieldData::class, $value, 'time');
        $this->assertEquals($value, \Lego\LegoRegister::get(FieldData::class, 'time'));
    }
}
