<?php

namespace Lego\Foundation\Match;

class MatchResults
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function add(string $value, string $label = null)
    {
        $this->options[$value] = is_null($label) ? $value : $label;
    }

    public function all()
    {
        $options = [];
        foreach ($this->options as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }
        return $options;
    }
}
