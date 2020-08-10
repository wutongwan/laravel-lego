<?php

use Illuminate\Support\Facades\Request;
use Lego\Demo\Models\Suite;
use Lego\Lego;

$suite = Suite::findOrNew(Request::query('id'));
$form = Lego::form($suite);
$form->addText('address', '地址')
    ->unique()
    ->required()
    ->placeholder('地址唯一，不可重复');
$form->addSelect('type', '类型')->values(Suite::listType());
$form->addSelect('status', '状态')->values(Suite::listStatus());
$form->addTextarea('note', '备注');

$form->required(['type', 'status']);
$form->success(route('demo', 'suite-list'));

if ($suite->id) {
    $form->addRightTopButton('删除', route('demo', 'suite-delete') . '?id=' . $suite->id);
}

return $form;
