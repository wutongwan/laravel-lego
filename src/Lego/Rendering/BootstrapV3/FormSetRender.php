<?php

namespace Lego\Rendering\BootstrapV3;

use Lego\Set\Form;

class FormSetRender
{
    public function render(Form $form)
    {
        return view('lego::bootstrap-v3.form', ['form' => $form]);
    }
}
