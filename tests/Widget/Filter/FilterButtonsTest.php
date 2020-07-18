<?php

namespace Lego\Tests\Widget\Filter;

use Lego\Tests\TestCase;
use Lego\Widget\Filter;

class FilterButtonsTest extends TestCase
{
    public function testResetButton()
    {
        $default = $this->render2html(new Filter([]));
        $this->assertStringContainsString('href="http://localhost"', $default);
        $this->assertStringContainsString('清空', $default);

        $form = new Filter([]);
        $form->resetText('双击666');
        $changed = $this->render2html($form);
        $this->assertStringContainsString('双击666', $changed);
    }
}
