<?php

namespace Lego\Field\Provider;

use Illuminate\Support\HtmlString;
use Lego\Foundation\Facades\LegoAssets;

class RichText extends Textarea
{
    protected function renderReadonly()
    {
        return new HtmlString($this->takeShowValue());
    }

    protected function renderEditable()
    {
        LegoAssets::js('components/tinymce/tinymce.min.js');

        return $this->view('lego::default.field.tinymce');
    }
}
