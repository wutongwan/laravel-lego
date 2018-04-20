<?php namespace Lego\Tests\Operator;

use Lego\Operator\Query\EloquentQuery;
use Lego\Tests\Models\ExampleModel;
use Lego\Tests\TestCase;

class EloquentQueryTest extends TestCase
{
    public function testWhere()
    {
        $query = ExampleModel::query();

        $legoQuery = EloquentQuery::attempt($query);
        $legoQuery
            ->whereEquals('equals', 'equals_value')
            ->whereIn('in_column', ['in_value1', 'in_value2'])
            ->whereGt('gt', 'gt_value')
            ->whereGte('gte', 'gte_value')
            ->whereLt('lt', 'lt_value')
            ->whereLte('lte', 'lte_value')
            ->whereBetween('between', 'between_min', 'between_max')
            ->whereContains('contains', 'contains_value')
            ->whereStartsWith('starts_with', 'starts_with_value')
            ->whereEndsWith('ends_with', 'ends_with_value')
            ->whereEquals('json:key1:key2', 'json_value')
            ->whereEquals('test_belongs_to.name', 'tbt_name_value')
            ->whereContains('test_belongs_to.tbt_json:key3', 'tbt_json_value');

        self::assertSame(
            'select * from `example_models` '
            . 'where `equals` = ? and `in_column` in (?, ?) and `gt` > ? and `gte` >= ? '
            . 'and `lt` < ? and `lte` <= ? and `between` between ? '
            . 'and `contains` like ? and `starts_with` like ? and `ends_with` like ? '
            . 'and `json`->\'$."key1"."key2"\' = ? '
            . 'and exists (select * from `belongs_to_examples` '
            . 'where `example_models`.`test_belongs_to_id` = `belongs_to_examples`.`id` '
            . 'and `name` = ?) '
            . 'and exists (select * from `belongs_to_examples` '
            . 'where `example_models`.`test_belongs_to_id` = `belongs_to_examples`.`id` '
            . 'and `tbt_json`->\'$."key3"\' like ?)',
            $query->toSql()
        );

        self::assertSame([
            'equals_value',
            'in_value1',
            'in_value2',
            'gt_value',
            'gte_value',
            'lt_value',
            'lte_value',
            'between_min',
            'between_max',
            '%contains_value%',
            'starts_with_value%',
            '%ends_with_value',
            'json_value',
            'tbt_name_value',
            '%tbt_json_value%',
        ], $query->getBindings());
    }
}
