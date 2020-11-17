<?php

namespace Lego\Input\Form;

use Lego\Foundation\Match\MatchQuery;

class ColumnAutoCompleteHandler extends AutoCompleteHandler
{
    public function afterAdd()
    {
        // set default match
        $this->input->match(function (MatchQuery $query) {
            return $this->wrapper->getAdaptor()->queryMatch(
                $this->input->getFieldName(),
                $query
            );
        });
    }
}
