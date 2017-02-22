<?php namespace Lego\Field\Provider;

use Collective\Html\HtmlFacade;

class Hidden extends Text
{
    protected $inputType = 'hidden';

    protected function renderReadonly()
    {
        return HtmlFacade::tag('div', (string)parent::renderReadonly(), ['class' => 'hide']);
    }
}
