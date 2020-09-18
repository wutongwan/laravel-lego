<?php

namespace Lego\Demo;

use Collective\Html\HtmlServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Lego\LegoServiceProvider;
use Lego\Widget\Widget;

class DemoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(LegoServiceProvider::class);
        $this->app->register(HtmlServiceProvider::class);
    }

    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'lego-demo');
        $router->get('/init-database', '\Lego\Demo\DemoController@initDatabase')->name('init-database');
        $router->any('/{item?}', '\Lego\Demo\DemoController@demo')->name('demo');
    }
}
