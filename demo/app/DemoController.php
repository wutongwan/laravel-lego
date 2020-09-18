<?php

namespace Lego\Demo;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Lego\Widget\Widget;

class DemoController
{
    /**
     * @var string
     */
    private $demos;

    public function __construct(Factory $view)
    {
        $this->demos = require __DIR__ . '/../demos/0.register.php';
        $view->share('demos', $this->demos);
    }

    public function demo(string $item = '')
    {
        $item ? abort_unless(isset($this->demos[$item]), 404) : ($item = array_key_first($this->demos));

        if (!file_exists(config('database.connections.sqlite.database'))) {
            return redirect(action(
                '\Lego\Demo\DemoController@initDatabase',
                ['back' => \Illuminate\Support\Facades\Request::fullUrl()]
            ));
        }

        $path = __DIR__ . "/../demos/{$item}.php";
        $data = [
            'title' => $this->demos[$item],
            'widget' => $widget = require $path,
            'code' => trim(implode("\n", array_slice(explode("\n", file_get_contents($path)), 1))),
        ];
        return $widget instanceof Widget
            ? $widget->view('lego-demo::demo', $data)
            : view('lego-demo::demo', $data);
    }

    public function initDatabase(Request $request)
    {
        // recreate db file
        $path = config('database.connections.sqlite.database');
        file_exists($path) && unlink($path);
        touch($path);

        require __DIR__ . '/../databases/migrations.php';   // run migrations
        require __DIR__ . '/../databases/seeders.php';      // init test data

        if ($back = $request->query('back')) {
            return redirect($back);
        } else {
            return "Database Initialized";
        }
    }
}
