<?php

use Lego\Lego;
use Lego\Widget\Form;

$form = Lego::form();

$form->addSelect('method', 'Method')
    ->placeholder('Request Method')
    ->values('get', 'post')
    ->required();

$form
    ->when('method', '=', 'get', function (Form $form) {
        $form->addText('url')->required();
        $form->addTextarea('header');
    })
    ->when('method', 'in', ['post'], function (Form $form) {
        $form->addTextarea('reason')->default('what do you want ?');
    });

$form->onSubmit(function () {
    return view('lego-demo::request-data');
});

return $form;


