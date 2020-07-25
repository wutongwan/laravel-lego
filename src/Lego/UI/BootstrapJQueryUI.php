<?php

namespace Lego\UI;

class BootstrapJQueryUI
{
    public static function scripts(): array
    {
        $manifest = json_decode(file_get_contents(__DIR__ . '/../../../public/build/manifest.json'), true);

        return [
            'build/' . $manifest['vendors~index.js'],
            'build/' . $manifest['index.js'],
        ];
    }
}
