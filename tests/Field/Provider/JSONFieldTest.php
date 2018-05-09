<?php namespace Lego\Tests\Field\Provider;

use Lego\Field\Provider\JSON;
use Lego\Foundation\Exceptions\LegoException;
use Lego\Tests\TestCase;

class JSONFieldTest extends TestCase
{
    public function testMain()
    {
        try {
            $field = new JSON('wrong');
            self::assertTrue(false, 'should not run');
        } catch (LegoException $e) {
        }

        $field = new JSON('right:key');

        // test input value
        $field->setOriginalValue('abcd');
        self::assertSame('abcd', $field->takeInputValue());

        $field->setOriginalValue('["a"]');
        self::assertSame(['a'], $field->takeInputValue());

        $field->setOriginalValue(['a']);
        self::assertSame(['a'], $field->takeInputValue());
    }
}
