<?php namespace Lego\Foundation;

class Assets
{
    const PATH_PREFIX = 'packages/wutongwan/lego';

    private $all = [];
    private $globals = [
        'style' => [
            'bootstrap' => 'components/bootstrap/dist/css/bootstrap.min.css',
        ],
        'script' => [
            'jQuery' => 'components/jquery/dist/jquery.min.js',
        ],
    ];

    public function __construct()
    {
        $this->reset();
    }

    public function css($path, $prefix = self::PATH_PREFIX)
    {
        $this->add('style', $path, $prefix);
    }

    public function js($path, $prefix = self::PATH_PREFIX)
    {
        $this->add('script', $path, $prefix);
    }

    public function scripts()
    {
        return $this->get('script');
    }

    public function styles()
    {
        return $this->get('style');
    }

    private function get($type)
    {
        return isset($this->all[$type]) ? array_values($this->all[$type]) : [];
    }

    public function reset($type = null)
    {
        if (is_null($type)) {
            $this->all = [];
            foreach (array_keys($this->globals) as $type) {
                self::reset($type);
            }
            return;
        }

        $this->all[$type] = [];
        if (isset($this->globals[$type])) {
            foreach ($this->globals[$type] as $name => $path) {
                if (config('lego.assets.global.' . $name)) {
                    $this->add($type, $path);
                }
            }
        }
    }

    private function add($type, $path, $prefix = self::PATH_PREFIX)
    {
        if (is_array($path)) {
            foreach ($path as $line) {
                $this->add($type, $line, $prefix);
            }
            return;
        }

        if (!isset($this->all[$type])) {
            self::reset($type);
        }

        if ($prefix) {
            $path = $prefix . '/' . ltrim($path, '/');
        }

        if (!in_array($path, $this->all[$type])) {
            $this->all[$type][] = $path;
        }
    }
}
