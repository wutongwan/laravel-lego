<?php

namespace Lego\Field\Concerns;

use Lego\Foundation\Facades\LegoAssets;

trait HasSelect2Assets
{
    protected function includeSelect2Assets()
    {
        LegoAssets::css('components/select2/dist/css/select2.min.css');
        LegoAssets::css('components/select2-bootstrap-theme/dist/select2-bootstrap.min.css');
        LegoAssets::js('components/select2/dist/js/select2.full.min.js');

        if ($this->localeIsNotEn()) {
            LegoAssets::js("components/select2/dist/js/i18n/{$this->getLocale()}.js");
        }
    }
}
