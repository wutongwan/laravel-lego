<?php

namespace Lego\UI;

class BootstrapJQueryUI
{
    public static function scripts(): array
    {
        $manifest = json_decode(file_get_contents(__DIR__ . '/../../../public/build/manifest.json'), true);

        return array_filter([
            $manifest['vendors~index.js'] ?? null,
            $manifest['index.js'],
        ]);
    }
}
