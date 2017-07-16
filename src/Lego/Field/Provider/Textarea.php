<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;

class Textarea extends Text
{
    protected function initialize()
    {
        $this->attr([
            'cols' => 50,
            'rows' => 10,
        ]);
    }

    public function cols($cols)
    {
        return $this->attr('cols', $cols);
    }

    public function getCols()
    {
        return $this->getAttribute('cols');
    }

    public function rows($rows)
    {
        return $this->attr('rows', $rows);
    }

    public function getRows()
    {
        return $this->getAttribute('rows');
    }

    protected function renderEditable()
    {
        return FormFacade::textarea(
            $this->elementName(),
            $this->takeInputValue(),
            $this->getAttributes()
        );
    }
}
