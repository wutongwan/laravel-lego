<?php

namespace Lego\Field\Concerns;

use Illuminate\Support\Facades\App;

/**
 * i18n.
 */
trait HasLocale
{
    protected $locale;

    protected function initializeHasLocale()
    {
        $this->locale(App::getLocale()); // set field's locale.
    }

    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function isLocale($locale)
    {
        return $this->locale === $locale;
    }

    protected function localeIsNotEn()
    {
        return !$this->isLocale('en');
    }
}
