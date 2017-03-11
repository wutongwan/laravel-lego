<?php namespace Lego\Field;

use Lego\Foundation\Exceptions\LegoException;

class Condition
{
    private $field;
    private $operator;
    private $expected;

    public function __construct(Field $field, $operator, $expected)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->expected = $expected;
    }

    public function field()
    {
        return $this->field;
    }

    public function operator()
    {
        return $this->operator;
    }

    public function expected()
    {
        return $this->expected;
    }

    public function pass()
    {
        $actual = $this->field->getNewValue();
        $expected = $this->expected;

        switch ($this->operator) {
            case '=':
            case '==':
            case '===':
                return $actual === $expected;
            case '!=':
            case '!==':
                return $actual !== $expected;
            case '>':
                return $actual > $expected;
            case '>=':
                return $actual >= $expected;
            case '<':
                return $actual < $expected;
            case '<=':
                return $actual <= $expected;
            case 'in':
                return in_array($actual, $expected);
            default:
                throw new LegoException("Unsupported operator `{$this->operator}`");
        }
    }

    public function fail()
    {
        return !$this->pass();
    }
}
