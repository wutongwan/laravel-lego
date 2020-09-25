<?php
use Lego\Demo\Models\Suite;
use Lego\Lego;

$form = Lego::formV2(Suite::query()->first());

$form->addText('address', 'Address');

return $form;
