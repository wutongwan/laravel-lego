<?php

namespace Lego\Tests\Field;

use Lego\Field\Concerns\HasOptions;
use Lego\Field\Provider\Text;
use Lego\Tests\TestCase;

class HasOptionsTest extends TestCase
{
    public function testValues()
    {
        $field = new HasOptionsExample('test');

        $field->values([1, 2, 3]);
        $this->assertEquals([1 => 1, 2 => 2, 3 => 3], $field->getOptions());

        $field->options(['abc' => 'A B C']);
        $this->assertEquals(['abc' => 'A B C'], $field->getOptions());

        $this->assertEquals('A B C', $field->getOptionLabelByValue('abc'));
    }
}

class HasOptionsExample extends Text
{
    use HasOptions;
}
