<?php

namespace Lego\Tests\Widget\Filter;

use Lego\Tests\TestCase;
use Lego\Widget\Filter;

class FilterButtonsTest extends TestCase
{
    public function testResetButton()
    {
        $default = $this->render2html(new Filter([]));
        $this->assertContains('href="http://localhost"', $default);
        $this->assertContains('清空', $default);

        $form = new Filter([]);
        $form->resetText('双击666');
        $changed = $this->render2html($form);
        $this->assertContains('双击666', $changed);
    }
}
