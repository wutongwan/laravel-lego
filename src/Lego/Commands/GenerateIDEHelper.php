<?php namespace Lego\Commands;

use Illuminate\Console\Command;
use Lego\Register\UserDefinedField;

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
        $path = base_path('_ide_helper_lego.php');
        UserDefinedField::registerFromConfiguration();
        $fields = UserDefinedField::list();
        if (!$fields) {
            $this->line('Does not exists user defined fields');
            if (is_file($path)) {
                unlink($path);
            }
            return;
        }

        $content = view('lego::commands.ide-helper.layout')->with('fields', $fields);
        file_put_contents($path, $content);
        $this->line("Lego IDE Helper file `{$path}` done.");
    }
}
