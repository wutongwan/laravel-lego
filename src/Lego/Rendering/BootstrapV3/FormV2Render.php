<?php

namespace Lego\Rendering\BootstrapV3;

use Lego\Widget\FormV2;

class FormV2Render
{
    public function render(FormV2 $form)
    {
        return view('lego::bootstrap-v3.form', ['form' => $form]);
    }
}
