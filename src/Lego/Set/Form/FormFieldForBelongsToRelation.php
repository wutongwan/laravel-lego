<?php

namespace Lego\Set\Form;

use Lego\Contracts\Input\MatchAble;
use Lego\DataAdaptor\DataAdaptor;
use Lego\Foundation\FieldName;
use Lego\Foundation\Match\MatchQuery;
use Lego\Input\Input;

class FormFieldForBelongsToRelation extends FormField
{
    /**
     * FormFieldForBelongsToRelation constructor.
     * @param Input|MatchAble $input
     * @param FieldName $fieldName
     * @param string $label
     * @param DataAdaptor $adaptor
     */
    public function __construct(Input $input, FieldName $fieldName, string $label, DataAdaptor $adaptor)
    {
        $input->match(function (MatchQuery $match) use ($adaptor, $fieldName) {
            return $adaptor->queryMatch($fieldName, $match);
        });

        parent::__construct($input, $fieldName, $label, $adaptor);
    }
}
