<?php

namespace Lego\Input\Form;

use Lego\Input\OneToOneRelation;
use PhpOption\Option;

class OneToOneRelationHandler extends ColumnAutoCompleteHandler
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
                $this->wrapper->getAdaptor()->getKeyName($this->input->getFieldName())
            )
        );
    }

    public function beforeRender(): void
    {
        parent::beforeRender();

        $text = $this->wrapper->getAdaptor()->getFieldValue($this->input->getFieldName());
        if ($text->isDefined()) {
            $this->input->setTextValue($text->get());
        }
    }

    public function readOriginalValueFromAdaptor(): Option
    {
        return $this->wrapper->getAdaptor()->getFieldValue(
            $this->input->getValueFieldName()
        );
    }

    public function writeInputValueToAdaptor($value): void
    {
        $this->wrapper->getAdaptor()->setRelated($this->input->getFieldName(), $value);
    }
}
