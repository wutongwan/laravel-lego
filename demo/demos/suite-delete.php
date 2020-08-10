<?php


use Illuminate\Support\Facades\Request;
use Lego\Demo\Models\Suite;
use Lego\Lego;

$id = Request::query('id');
if (!$id) {
    return Lego::message('Invalid argument', 'error');
}

$suite = Suite::find($id);
if (!$suite) {
    return Lego::message('Can not find suite', 'error');
}

return Lego::confirm(
    "确定删除公寓 #{$suite->id} {$suite->address} ？",
    function () use ($suite) {
        $suite->delete();
        return redirect(route('demo', 'suite-list'));
    }
);
