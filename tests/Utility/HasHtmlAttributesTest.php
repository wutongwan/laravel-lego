<?php

namespace Lego\Tests\Utility;

use Illuminate\Support\HtmlString;
use Lego\Tests\TestCase;

class HasHtmlAttributesTest extends TestCase
{
    public function testGetterSetter()
    {
        $eg = new ExampleUsedHasHtmlAttributes();
        $eg->setAttribute('class', 'class_a');
        self::assertEquals('class_a', $eg->getAttribute('class'));

        $eg->setAttribute('class', ['class_b']);
        self::assertEquals(['class_a', 'class_b'], $eg->getAttribute('class'));

        $eg->setAttribute('class', 'class_c');
        self::assertEquals('class_c', $eg->getAttribute('class'));

        $eg->setAttribute(['a' => 'b']);
        $eg->setAttribute(['a' => 'c']);
        self::assertEquals(['b', 'c'], $eg->getAttribute('a'));

        $eg->removeAttribute('class');
        self::assertEquals(null, $eg->getAttribute('class'));

        $eg->setAttribute('hello', 'zhwei');
        $hs = $eg->getAttributesString();
        self::assertInstanceOf(HtmlString::class, $hs);
        self::assertSame('a="b c" hello="zhwei"', $hs->toHtml());

        self::assertSame('', $eg->getAttributeString('none'));
        self::assertSame('default', $eg->getAttributeString('none', 'default'));

        $eg->setAttribute('zero', '0');
        self::assertSame('0', $eg->getAttributeString('zero'));
    }
}

class ExampleUsedHasHtmlAttributes
{
    use \Lego\Foundation\Concerns\HasHtmlAttributes;
}
