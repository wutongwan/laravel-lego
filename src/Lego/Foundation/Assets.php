<?php namespace Lego\Foundation;

class Assets
{
    const PATH_PREFIX = 'packages/wutongwan/lego';

    private $all = [];
    private $mix = [];
    private $globals = [
        'style' => [
            'bootstrap' => 'components/bootstrap/dist/css/bootstrap.min.css',
        ],
        'script' => [
            'jQuery' => 'components/jquery/dist/jquery.min.js',
            'bootstrap' => 'components/bootstrap/dist/js/bootstrap.min.js',
        ],
    ];

    public function __construct()
    {
        $manifest = file_get_contents(public_path(self::PATH_PREFIX . '/mix-manifest.json'));
        $this->mix = json_decode($manifest, JSON_OBJECT_AS_ARRAY);

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
            $this->reset($type);
        }

        if ($prefix) {
            $path = $prefix . $this->mix('/' . ltrim($path, '/'));
        }

        if (!in_array($path, $this->all[$type])) {
            $this->all[$type][] = $path;
        }
    }

    private function mix($path)
    {
        return isset($this->mix[$path]) ? $this->mix[$path] : $path;
    }
}
