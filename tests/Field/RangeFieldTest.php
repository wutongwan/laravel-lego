<?php namespace Lego\Tests\Field;

use Lego\Field\Provider\NumberRange;
use Lego\Tests\TestCase;

class RangeFieldTest extends TestCase
{
    public function testRangeFieldPlaceholder()
    {
        $nr = new NumberRange('abc', 'default description');

        $this->assertSame('default description', $nr->getUpper()->description());
        $this->assertSame('default description', $nr->getLower()->description());

        $this->assertSame(null, $nr->getLower()->getPlaceholder());
        $this->assertSame(null, $nr->getUpper()->getPlaceholder());

        $nr->placeholder('a');
        $this->assertSame('a', $nr->getLower()->getPlaceholder());
        $this->assertSame('a', $nr->getUpper()->getPlaceholder());

        $nr->placeholder(['b', 'c']);
        $this->assertSame('b', $nr->getLower()->getPlaceholder());
        $this->assertSame('c', $nr->getUpper()->getPlaceholder());
    }
}
