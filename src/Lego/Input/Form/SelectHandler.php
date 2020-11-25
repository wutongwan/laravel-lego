<?php

namespace Lego\Input\Form;

use Lego\Input\Select;
use UnexpectedValueException;

class SelectHandler extends TextHandler
{
    /**
     * @var Select
     */
    protected $input;

    public function beforeRender(): void
    {
        parent::beforeRender();

        $this->wrapper->validator(function ($selected) {
            if (empty($selected)) {
                return;
            }

            if ($this->input->isMultiSelect() && is_array($selected)) {
                foreach ($selected as $item) {
                    if (false === $this->isValidValue($this->input->getOptions(), $item)) {
                        throw new UnexpectedValueException('非法选项');
                    }
                }
                return;
            }

            if (false === $this->isValidValue($this->input->getOptions(), $selected)) {
                throw new UnexpectedValueException('非法选项');
            }
        });
    }

    private function isValidValue(array $options, $selected)
    {
        foreach ($options as $value => $text) {
            if (is_iterable($text) && $this->isValidValue($text, $selected)) {
                return true;
            } elseif ($value === $selected) {
                return true;
            }
        }
        return false;
    }
}
