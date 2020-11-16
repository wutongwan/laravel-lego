<?php

namespace Lego\Foundation\Button;

use Closure;
use Illuminate\Support\HtmlString;
use Lego\Foundation\Html\HtmlAttributes;

class Button
{
    /**
     * 按钮文案
     *
     * @var string
     */
    private $text;

    /**
     * 按钮链接
     *
     * @var string|null
     */
    private $url;

    /**
     * @var HtmlAttributes
     */
    private $attrs;

    public function __construct(string $text, string $url = null)
    {
        $this->text = $text;
        $this->url = $url;
        $this->attrs = new HtmlAttributes();
    }

    /**
     * @param Closure|null $closure
     * @psalm-var Closure(HtmlAttributes):void $closure
     *
     * @return HtmlAttributes
     */
    public function attrs(Closure $closure = null): HtmlAttributes
    {
        if ($closure) {
            $closure($this->attrs);
        }

        return $this->attrs;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return Button
     */
    public function setUrl(?string $url): Button
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Button
     */
    public function setText(string $text): Button
    {
        $this->text = $text;
        return $this;
    }

    public function render(string $className = null): HtmlString
    {
        $this->attrs->addClass($className);

        $html = $this->url
            ? sprintf('<a href="%s" %s>%s</a>', $this->url, $this->attrs, $this->text)
            : sprintf('<button %s>%s</button>', $this->attrs, $this->text);
        return new HtmlString($html);
    }
}
