<?php

namespace Lego\Tests;

use Lego\LegoServiceProvider;
use Lego\Tests\Tools\FakeMobileDetect;
use Lego\Widget\Widget;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // register lego
        $app->register(\Collective\Html\HtmlServiceProvider::class);
        $app->register(\Mews\Purifier\PurifierServiceProvider::class);
        $app->register(LegoServiceProvider::class);

        // add test view namespace
        $app->make('view')->addNamespace('lego-test', __DIR__ . '/resources/views');

        $app->bind(\Mobile_Detect::class, FakeMobileDetect::class);

        return $app;
    }

    protected function render2html(Widget $widget)
    {
        return $this->render($widget)->render();
    }

    protected function render(Widget $widget)
    {
        return $widget->view('lego-test::widget', ['widget' => $widget]);
    }

    protected function faker()
    {
        return \Faker\Factory::create($this->app->getLocale());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        FakeMobileDetect::forgetMocks();
    }
}
