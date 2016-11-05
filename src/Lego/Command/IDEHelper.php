<?php namespace Lego\Command;

use Illuminate\Console\Command;
use Lego\Register\Data\Field;

/**
 * 生成 Lego 的 IDE Helper 文件
 *
 * inspired by https://github.com/barryvdh/laravel-ide-helper
 *
 * Class IDEHelper
 * @package Lego\Command
 */
class IDEHelper extends Command
{
    protected $signature = 'lego:ide-helper';

    protected $description = 'Generate IDE Helper file for Lego.';

    public function handle()
    {
        $fields = Field::availableFields();
        $content = view('lego::command.ide-helper.layout', compact('fields'));
        $path = base_path('_ide_helper_lego.php');
        file_put_contents($path, $content);

        $this->line("Lego IDE Helper file `{$path}` done.");
    }
}