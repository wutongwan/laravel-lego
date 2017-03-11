<?php namespace Foundation;

use Lego\Field\Provider\Text;
use Lego\Field\Condition;

class ConditionTest extends \Lego\Tests\TestCase
{
    /**
     * @dataProvider conditionDataProvider
     */
    public function testMain($left, $operator, $right, $pass = true)
    {
        $field = new Text('test', 'Text', []);
        $field->setNewValue($left);
        $c = new Condition($field, $operator, $right);
        $this->assertEquals($pass, $c->pass());
    }

    public function conditionDataProvider()
    {
        return [
            [0, '=', 0],
            [null, '!=', ''],
            [0, '!==', '0'],
            [0, '===', '0', false],
            [0, '===', '0', false],
        ];
    }
}
