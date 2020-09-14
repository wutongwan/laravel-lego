<?php

use Symfony\Component\Console\Input\ArgvInput;

/**
 * Usage:
 *
 * - php demo/run.php
 * - php demo/run.php --host=0.0.0.0 --port=8888
 */

(function () {
    require __DIR__ . '/../vendor/autoload.php';

    // laravel folder full path
    $laravel = realpath(__DIR__ . '/../vendor/laravel/laravel');

    // update configs
    file_put_contents($laravel . '/.env', file_get_contents(__DIR__ . '/.env'));

    // fix `public/index.php` autoload path
    file_put_contents(
        $path = "{$laravel}/public/index.php",
        str_replace('/../vendor/autoload.php', '/../../../autoload.php', file_get_contents($path))
    );

    // register DemoServiceProvider
    file_put_contents($path, str_replace(
        $original = '$kernel = ',
        '$app->register(\Lego\Demo\DemoServiceProvider::class);' . "\n\n{$original}",
        file_get_contents($path)
    ));

    // create static files link
    $link = ($folder = $laravel . '/public/packages/wutongwan') . '/lego';
    file_exists($folder) || mkdir($folder, 0777, true);
    file_exists($link) && unlink($link);
    symlink(__DIR__ . '/../public', $link);

    // 支持传入 host 和 port 参数
    $input = new ArgvInput();
    $host = $input->getParameterOption('--host') ?: '127.0.0.1';
    $port = $input->getParameterOption('--port') ?: '8080';

    // start dev server
    chdir($laravel . '/public');
    passthru("php -S {$host}:{$port} " . $laravel . '/server.php');
})();
