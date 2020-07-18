<?php

namespace Lego\Tests\Widget\Form;

use Lego\Tests\TestCase;
use Lego\Widget\Form;

class FormButtonsTest extends TestCase
{
    public function testSubmitButton()
    {
        $default = $this->render2html(new Form([]));
        $this->assertStringContainsString('type="submit"', $default);
        $this->assertStringContainsString('提交', $default);

        $form = new Form([]);
        $form->submitText('双击666');
        $changed = $this->render2html($form);
        $this->assertStringContainsString('双击666', $changed);
    }
}
