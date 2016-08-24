<?php namespace Lego\Widget;

class Form extends Widget
{
    public function render() : string
    {
        return view('lego::form.bs-horizontal', ['form' => $this]);
    }
}