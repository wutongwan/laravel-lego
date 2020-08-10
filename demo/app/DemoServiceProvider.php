<?php

namespace Lego\Demo;

use Collective\Html\HtmlServiceProvider;
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
        $this->registerRoutes($router);
    }

    private function registerRoutes(Router $router)
    {
        // 初始化数据库
        $router->get('/init-database', function () {
            // recreate db file
            $path = config('database.connections.sqlite.database');
            file_exists($path) && unlink($path);
            touch($path);

            // run migrations
            require __DIR__ . '/../databases/migrations.php';

            // init test data
            require __DIR__ . '/../databases/seeders.php';
        })->name('init-database');

        // demo entry
        $router->any('/{item?}', function ($item = 'city') {
            abort_unless(isset($this->demos[$item]), 404);

            $path = __DIR__ . "/../demos/{$item}.php";
            $data = [
                'title' => $this->demos[$item],
                'widget' => $widget = require $path,
                'code' => trim(implode("\n", array_slice(explode("\n", file_get_contents($path)), 1))),
                'demos' => $this->demos,
            ];
            return $widget instanceof Widget
                ? $widget->view('lego-demo::demo', $data)
                : view('lego-demo::demo', $data);
        })->name('demo');
    }

    private $demos = [
        'city-list' => 'Filter & Grid：城市列表',
        'city' => 'Form：新建/编辑城市',
        'street-list' => 'Filter & Grid：街道列表',
        'street' => 'Form：新建/编辑街道',
        'suite-list' => 'Filter & Grid：公寓列表',
        'suite' => 'Form：新建/编辑公寓',
        'suite-delete' => 'Confirm：删除公寓',
        'confirm' => 'Confirm：确认操作示例',
        'grid-batch' => 'Grid：批处理',
        'message' => 'Message：提示信息',
        'condition-group' => 'Form：动态添加字段',
        'elastic-query' => 'Filter: ES 示例',
        'form-cascade-select' => 'Form: 级联输入',
        'form-rich-text' => 'Form: 富文本输入',
    ];
}
