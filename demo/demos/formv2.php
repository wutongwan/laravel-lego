<?php
// zhangwei@danke.com


use Lego\Demo\Models\Suite;
use Lego\Lego;

$form = Lego::formV2(new Suite());

$form->addText('name', 'User Name')
    ->readonly();
