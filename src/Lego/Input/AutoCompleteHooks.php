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
            $this->setText($v);
        }
    }

    protected function setText(string $text)
    {
        $this->input->values()->setExtra('text', $text);
    }

    public function onSubmit(Request $request): void
    {
        parent::onSubmit($request);

        $this->setText(
            $request->input($this->input->getTextInputName())
        );
    }
}
