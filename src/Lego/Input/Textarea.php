<?php

declare(strict_types=1);

namespace Lego\Input;

class Textarea extends Text
{
    public function rows(int $rows)
    {
        $this->attributes()->set('rows', $rows);
        return $this;
    }

    protected function viewName(): string
    {
        return 'lego::bootstrap3.input.textarea';
    }
}
