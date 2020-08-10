<?php

use Lego\Lego;

$form = Lego::form([
    'html' => '',
]);

$province = $form->addRichText('html')
    ->required();

return $form->onSubmit(function () {
    return view('request-data');
});
