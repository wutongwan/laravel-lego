<?php

namespace Lego\Tests\Field\Provider;

use Lego\Field\Provider\Text;
use Lego\Tests\TestCase;

class TextFieldTest extends TestCase
{
    public function testRender()
    {
        $field = new Text('abc');
        $field->process();

        self::assertSame(
            '<input id="lego-abc" name="abc" lego-type="Field" lego-field-type="Text" lego-field-mode="editable" class="form-control" type="text">',
            (string) $field->render()
        );

        $readonlyString = '<p id="lego-abc" class="form-control-static"></p>';

        $field->readonly();
        self::assertEquals($readonlyString, (string) $field->render());

        $field = new Text('abc');
        $field->disabled();
        $field->process();
        self::assertSame($readonlyString, (string) $field->render());
    }
}
