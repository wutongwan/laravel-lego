<?php namespace Lego\Commands;

use Illuminate\Console\Command;
use Lego\LegoServiceProvider;

class UpdateComponents extends Command
{
    protected $signature = 'lego:update-components {--bower-allow-root : bower update allow run as root user.}';

    protected $description = 'bower update && publish';

    public function handle()
    {
        $command = 'cd vendor/wutongwan/lego && bower update -V';
        if ($this->option('bower-allow-root')) {
            $command .= ' --allow-root';
        }
        $this->exec($command . ' && cd -');

        $this->line('~ publish public files');
        $this->call('vendor:publish', [
            '--provider' => LegoServiceProvider::class,
            '--tag' => 'public',
            '--force' => true,
        ]);
    }

    protected function exec($command)
    {
        $this->line("~ $command");

        exec($command, $output, $code);
        $output = implode(PHP_EOL, $output);

        if ($code === 0) {
            return $this;
        } else {
            $this->error('Error: ' . $output);
            die();
        }
    }
}
