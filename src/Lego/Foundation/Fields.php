<?php

namespace Lego\Foundation;

use Illuminate\Support\Collection;
use Lego\Field\Field;

class Fields extends Collection
{
    public function fields()
    {
        return $this;
    }

    public function field($name)
    {
        return $this->get($name);
    }
}
