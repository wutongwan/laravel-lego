<?php

use Collective\Html\HtmlFacade;
use Illuminate\Support\Facades\Request;
use Lego\Lego;

return Lego::confirm(
    'Are you happy ?', // confirm 的提示信息
    function ($sure) {
        $em = $sure ? '^_^' : '-_-';
        return HtmlFacade::tag('h1', $em);
    },
    // 强制等待 n 秒后才可操作
    Request::query('confirm-delay', 0)
);
