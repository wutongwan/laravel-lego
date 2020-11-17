<?php

namespace Lego\Rendering\Bootstrap3;

use Lego\Set\Form\Form;

class FormSetRender
{
    public function render(Form $form)
    {
        return view('lego::bootstrap3.form', ['form' => $form]);
    }
}
