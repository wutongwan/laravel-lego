<?php

namespace Lego\Foundation;

use Collective\Html\HtmlFacade;
use Illuminate\Support\Facades\View;
use Lego\Register\HighPriorityResponse;

/**
 * Html button or link.
 */
class Button implements \JsonSerializable
{
    /**
     * button link.
     */
    protected $url;

    /**
     * button text.
     */
    protected $text;

    /**
     * button id.
     */
    protected $id;

    protected $attributes = [];

    /**
     * 防止重复点击.
     *
     * @var bool
     */
    protected $preventRepeatClick = false;

    public function __construct($text, $url = null, $id = null)
    {
        $this->text = $text;
        $this->id = $id ?: md5('_lego_button ' . $text);

        if ($url instanceof \Closure) {
            $this->action($url);
        } else {
            $this->url($url);
        }
    }

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function id($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * set html attribute.
     *
     * @param $name
     * @param $value
     * @param bool $merge if true, new `value` will be merge to current `value`
     *
     * @return $this
     */
    public function attribute($name, $value, $merge = true)
    {
        if (!$merge) {
            $this->attributes[$name] = $value;

            return $this;
        }

        $current = array_get($this->attributes, $name);
        if (is_array($current) && is_array($value)) {
            $current = array_unique(array_merge($current, $value));
        } elseif (is_string($current) && is_string($value)) {
            $current = trim($current) . ' ' . trim($value);
        } else {
            $current = $value;
        }
        $this->attributes[$name] = $current;

        return $this;
    }

    public function class($class)
    {
        return $this->attribute('class', is_array($class) ? $class : explode(' ', trim($class)));
    }

    public function removeClass($class)
    {
        $all = array_get($this->attributes, 'class', []);
        if (!$all) {
            return;
        }

        $idx = array_search($class, $all);
        if ($idx !== false) {
            unset($this->attributes['class'][$idx]);
        }
    }

    public function openInNewTab()
    {
        return $this->attribute('target', '_blank', false);
    }

    public function action(\Closure $action)
    {
        $resp = lego_register(HighPriorityResponse::class, $action, md5('button ' . $this->text));

        return $this->url($resp->url());
    }

    /** BootStrap Helpers */
    public function bootstrapStyle($style)
    {
        $styles = ['default', 'primary', 'info', 'warning', 'danger'];
        foreach ($styles as $sty) {
            $this->removeClass('btn-' . $sty);
        }

        return $this->class(['btn', 'btn-' . $style]);
    }

    /**
     * @param null|string $size if null, clear btn-*size* styles
     *
     * @return Button
     */
    public function bootstrapSize($size = null)
    {
        $styles = ['sm', 'xs', 'lg'];
        foreach ($styles as $style) {
            $this->removeClass('btn-' . $style);
        }

        return is_null($size) ? $this : $this->class('btn-' . $size);
    }

    /**
     * 防止重复点击.
     *
     * @param bool $condition
     *
     * @return $this
     */
    public function preventRepeatClick(bool $condition = true)
    {
        $this->preventRepeatClick = $condition;

        return $this;
    }

    public function isPreventRepeatClick()
    {
        return $this->preventRepeatClick;
    }

    public function __toString()
    {
        $this->attributes['id'] = $this->id;
        $attributes = $this->getFlattenAttributes();
        $attributes = HtmlFacade::attributes($attributes);

        /** @var \Illuminate\Contracts\View\View $view */
        $view = View::make('lego::default.button', ['button' => $this, 'attributes' => $attributes]);

        return $view->render();
    }

    protected function getFlattenAttributes()
    {
        return array_map(
            function ($value) {
                return is_array($value) ? join(' ', $value) : $value;
            },
            $this->attributes
        );
    }

    public function jsonSerialize()
    {
        return [
            'id'         => $this->id,
            'url'        => $this->url,
            'text'       => $this->text,
            'attributes' => $this->getFlattenAttributes(),
        ];
    }
}
