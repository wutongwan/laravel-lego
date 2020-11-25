<?php

namespace Lego\Foundation\Html;

trait HasHtmlAttributes
{
    /**
     * @var HtmlAttributes
     */
    private $htmlAttributes;

    protected function initializeHtmlAttributes()
    {
        $this->htmlAttributes = new HtmlAttributes();
    }

    public function getHtmlAttributes()
    {
        return $this->htmlAttributes->all();
    }

    public function attribute(string $name, $value)
    {
        $this->htmlAttributes->set($name, $value);
        return $this;
    }

    public function class(string ...$classes)
    {
        foreach ($classes as $class) {
            $this->htmlAttributes->addClass($class);
        }
        return $this;
    }
}
