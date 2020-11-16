<?php

namespace Lego\Input;

use PhpOption\Option;

class OneToOneRelationHooks extends ColumnAutoCompleteHooks
{
    /**
     * @var OneToOneRelation
     */
    protected $input;

    public function readOriginalValueFromAdaptor(): Option
    {
        return $this->input->getAdaptor()->getFieldValue(
            $this->input->getValueFieldName()
        );
    }

    public function writeInputValueToAdaptor($value): void
    {
        $this->input->getAdaptor()->setRelated($this->input->getValueFieldName(), $value);
    }
}
