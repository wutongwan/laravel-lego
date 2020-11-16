<?php

namespace Lego\Foundation;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class Values
{
    /**
     * @var Option
     */
    private $input;

    /**
     * @var Option
     */
    private $original;

    /**
     * @var array
     * @psalm-var array<string, mixed>
     */
    private $extra = [];

    public function __construct()
    {
        $this->input = None::create();
        $this->original = None::create();
    }

    public function setInputValue($input): void
    {
        $this->input = new Some($input);
    }

    public function getInputValue($default = null)
    {
        return $this->input->getOrElse($default);
    }

    public function isInputValueExists()
    {
        return $this->input->isDefined();
    }

    public function setOriginalValue($original)
    {
        $this->original = new Some($original);
    }

    public function getOriginalValue($default = null)
    {
        return $this->original->getOrElse($default);
    }

    public function isOriginalValueExists()
    {
        return $this->original->isDefined();
    }

    public function getCurrentValue()
    {
        return $this->isInputValueExists() ? $this->getInputValue() : $this->getOriginalValue();
    }

    public function getExtra(string $key, $default = null)
    {
        return $this->extra[$key] ?? $default;
    }

    public function setExtra(string $key, $value)
    {
        $this->extra[$key] = $value;
    }

    public function unsetExtra(string $key)
    {
        unset($this->extra[$key]);
    }

    public function isExtraExists(string $key)
    {
        return array_key_exists($key, $this->extra);
    }
}
