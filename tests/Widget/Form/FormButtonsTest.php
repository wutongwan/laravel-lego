<?php

namespace Lego\Tests\Widget\Form;

use Lego\Tests\TestCase;
use Lego\Widget\Form;

class FormButtonsTest extends TestCase
{
    public function testSubmitButton()
    {
        $default = $this->render2html(new Form([]));
        $this->assertContains('type="submit"', $default);
        $this->assertContains('提交', $default);

        $form = new Form([]);
        $form->submitText('双击666');
        $changed = $this->render2html($form);
        $this->assertContains('双击666', $changed);
    }
}
