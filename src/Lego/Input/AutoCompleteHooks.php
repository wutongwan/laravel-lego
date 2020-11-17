<?php

namespace Lego\Input;

use Illuminate\Http\Request;
use InvalidArgumentException;

class AutoCompleteHooks extends InputHooks
{
    /**
     * @var AutoComplete
     */
    protected $input;

    public function beforeRender(): void
    {
        if (!$this->input->getRemoteUrl()) {
            throw new InvalidArgumentException("AutoComplete input need `match` callback");
        }

        if ($v = $this->input->values()->getOriginalValue()) {
            $this->input->setTextValue($v);
        }
    }

    public function onSubmit(Request $request): void
    {
        parent::onSubmit($request);

        if ($textValue = $request->input($this->input->getTextInputName())) {
            $this->input->setTextValue($textValue);
        }
    }
}
