<?php

namespace Lego\Widget\Grid;

use Illuminate\Support\Str;

class Pipes4Features extends Pipes
{
    public function handleFormat($format)
    {
        return FormatTool::format($this->value(), $format, $this->cell()->store());
    }

    public function handleLink($url, $openInNewTab = false)
    {
        return sprintf(
            '<a href="%s" target="%s">%s</a>',
            $this->handleFormat($url),
            $openInNewTab ? '_blank' : '_self',
            $this->value()
        );
    }

    public function handleTag(array $mappings)
    {
        $selected = null;
        $value = $this->value();
        foreach ($mappings as $pattern => $style) {
            if (Str::is($pattern, $value)) {
                $selected = $style;
                break;
            }
        }
        if ($selected) {
            $value = sprintf('<span class="label label-%s">%s</span>', $selected, $value);
        }
        return $value;
    }
}
