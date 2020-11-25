<?php

namespace Lego\Foundation\Html;

use Illuminate\Support\HtmlString;

class HtmlTag
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $content;

    /**
     * @var HtmlAttributes
     */
    private $attributes;

    public function __construct(string $name, string $content = '')
    {
        $this->name = $name;
        $this->content = $content;
        $this->attributes = new HtmlAttributes();
    }

    public static function create(string $name, string $content): self
    {
        return new self($name, $content);
    }

    public function attributes(): HtmlAttributes
    {
        return $this->attributes;
    }

    public function __toString()
    {
        return sprintf('<%s %s>%s</%s>', $this->name, $this->attributes, $this->content, $this->name);
    }

    public function toHtmlString(): HtmlString
    {
        return new HtmlString((string)$this);
    }
}
