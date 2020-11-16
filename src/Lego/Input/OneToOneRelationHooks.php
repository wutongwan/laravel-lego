<?php

namespace Lego\Input;

use PhpOption\Option;

class OneToOneRelationHooks extends ColumnAutoCompleteHooks
{
    /**
     * @var OneToOneRelation
     */
    protected $input;

    public function afterAdd()
    {
        parent::afterAdd();

        $this->input->setValueFieldName(
            $this->input->getFieldName()->cloneWith(
                $this->input->getAdaptor()->getKeyName($this->input->getFieldName())
            )
        );
    }

    public function beforeRender(): void
    {
        parent::beforeRender();

        $text = $this->input->getAdaptor()->getFieldValue($this->input->getFieldName());
        if ($text->isDefined()) {
            $this->setText($text->get());
        }
    }

    public function readOriginalValueFromAdaptor(): Option
    {
        return $this->input->getAdaptor()->getFieldValue(
            $this->input->getValueFieldName()
        );
    }

    public function writeInputValueToAdaptor($value): void
    {
        $this->input->getAdaptor()->setRelated($this->input->getFieldName(), $value);
    }
}
