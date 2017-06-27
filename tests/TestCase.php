<?php

namespace Lego\Tests;

use Lego\Widget\Widget;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // register lego
        $app->register(\Collective\Html\HtmlServiceProvider::class);
        $app->register(\Lego\LegoServiceProvider::class);

        // add test view namespace
        $app->make('view')->addNamespace('lego-test', __DIR__ . '/resources/views');

        return $app;
    }

    protected function render(Widget $widget)
    {
        return $widget->view('lego-test::widget', ['widget' => $widget]);
    }
}
