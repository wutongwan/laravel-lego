<?php

namespace Lego\Tests\Input;

use Lego\Input\Text;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    public function testValues()
    {
        $input = new Text();
        self::assertFalse($input->isInputValueExists());
        self::assertFalse($input->isOriginalValueExists());
        self::assertSame(null, $input->getOriginalValue());
        self::assertSame(null, $input->getInputValue());

        $input = new Text();
        $input->setOriginalValue(null);
        $input->setInputValue(null);
        self::assertTrue($input->isInputValueExists());
        self::assertTrue($input->isOriginalValueExists());
        self::assertSame(null, $input->getOriginalValue());
        self::assertSame(null, $input->getInputValue());

        $input = new Text();
        $input->setOriginalValue(0);
        $input->setInputValue(0);
        self::assertTrue($input->isInputValueExists());
        self::assertTrue($input->isOriginalValueExists());
        self::assertSame(0, $input->getOriginalValue());
        self::assertSame(0, $input->getInputValue());
    }

    public function testGetValue()
    {
        $input = new Text();
        self::assertNull($input->getCurrentValue());

        $input->setOriginalValue(1);
        self::assertSame(1, $input->getCurrentValue());

        $input->setInputValue(100);
        self::assertSame(100, $input->getCurrentValue());

        $input->setOriginalValue(10);
        self::assertSame(100, $input->getCurrentValue());

        $input = new Text();
        $input->setInputValue(null);
        $input->setOriginalValue(100);
        self::assertSame(null, $input->getCurrentValue());
    }
}
