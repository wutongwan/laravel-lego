<?php

namespace Lego\Input;

use Lego\Foundation\Match\MatchQuery;

class ColumnAutoCompleteHooks extends AutoCompleteHooks
{
    public function afterAdd()
    {
        // set default match
        $this->input->match(function (MatchQuery $query) {
            return $this->input->getAdaptor()->queryMatch($this->input->getFieldName(), $query);
        });
    }
}
