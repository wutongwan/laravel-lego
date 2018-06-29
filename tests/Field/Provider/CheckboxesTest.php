<?php

namespace Lego\Tests\Field\Provider;

use Lego\Field\Provider\Checkboxes;
use Lego\Tests\TestCase;

class CheckboxesTest extends TestCase
{
    public function testAbc()
    {
        $field = new Checkboxes('abc');
        $field->options([1 => '一', 2 => '二']);
        $field->setNewValue([1]);
        $field->process();

        $this->assertContains('checked', $field->render()->render());
    }
}
