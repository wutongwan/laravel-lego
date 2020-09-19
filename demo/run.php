<?php

use Lego\Demo\DemoServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Usage:
 *
 * - php demo/run.php
 * - php demo/run.php --host=0.0.0.0 --port=8888
 */

(function () {
    require __DIR__ . '/../vendor/autoload.php';

    // 支持传入 host 和 port 参数
    $input = new ArgvInput();
    $host = $input->getParameterOption('--host') ?: '127.0.0.1';
    $port = $input->getParameterOption('--port') ?: '8080';

    // laravel folder full path
    $laravel = realpath(__DIR__ . '/../vendor/laravel/laravel');

    // create env file link
    @unlink($link = $laravel . '/.env.lego');
    symlink(__DIR__ . '/.env', $link);

    // create autoload file
    file_exists($vendor = "{$laravel}/vendor") || mkdir($vendor);
    file_put_contents("{$vendor}/autoload.php", "<?php require __DIR__ . '/../../../autoload.php';\n");

    // register DemoServiceProvider (put into packages cache)
    file_put_contents(
        "{$laravel}/bootstrap/cache/packages.php",
        sprintf("<?php return %s;", var_export(['wutongwan/lego-demo' => ['providers' => [DemoServiceProvider::class]]], true))
    );

    // create static files link
    $link = ($folder = $laravel . '/public/packages/wutongwan') . '/lego';
    file_exists($folder) || mkdir($folder, 0777, true);
    @unlink($link);
    symlink(__DIR__ . '/../public', $link);

    chdir($laravel . '/public');
    passthru("APP_ENV=lego php -S {$host}:{$port} {$laravel}/server.php");
})();
