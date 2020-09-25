<?php

namespace Lego\Foundation\Message;

trait HasMessages
{
    /**
     * @var Messages
     */
    private $messages;

    private function initializeHasMessages()
    {
        $this->messages = new Messages();
    }

    /**
     * @return Messages|Message[]
     */
    public function messages(): Messages
    {
        return $this->messages;
    }
}
