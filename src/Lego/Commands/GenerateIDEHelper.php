<?php namespace Lego\Commands;

use Illuminate\Console\Command;
use Lego\Foundation\Button;
use Lego\Foundation\Facades\LegoFields;
use Lego\Widget\Filter;
use Lego\Widget\Form;
use Lego\Widget\Grid\Grid;
use Lego\Widget\Widget;

/**
 * 生成 Lego 的 IDE Helper 文件
 *
 * inspired by https://github.com/barryvdh/laravel-ide-helper
 *
 * Class GenerateIDEHelper
 * @package Lego\Command
 */
class GenerateIDEHelper extends Command
{
    protected $signature = 'lego:generate-ide-helper';

    protected $description = 'Generate IDE Helper file for Lego.';

    public function handle()
    {
        $fields = LegoFields::all();
        $widgets = $this->widgetHelpers();
        $content = view('lego::commands.ide-helper.layout', compact('fields', 'widgets'));

        $path = base_path('_ide_helper_lego.php');
        file_put_contents($path, $content);
        $this->line("Lego IDE Helper file `{$path}` done.");
    }

    private function widgetHelpers()
    {
        $widgets = [];
        $list = [Filter::class, Form::class, Grid::class];

        foreach ($list as $widget) {
            $methods = [];
            /** @var Widget $instance */
            $instance = new $widget([]);
            foreach ($instance->buttonLocations() as $location) {
                $methods[] = [
                    'name' => 'add' . ucfirst(camel_case($location)) . 'Button',
                    'return' => '\\' . Button::class,
                    'arguments' => '$text, $url = null, $id = null',
                ];
            }
            array_set($widgets, "{$widget}.methods", $methods);

            $rft = new \ReflectionClass($instance);
            array_set($widgets, "{$widget}.namespace", $rft->getNamespaceName());
        }

        return $widgets;
    }
}
