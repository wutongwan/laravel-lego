<?php

use Illuminate\Support\Facades\Request;

return \Lego\Lego::message(
    Request::query('message', 'Have a Nice Day.'),
    Request::query('level') // level could be info, warning, danger
);
