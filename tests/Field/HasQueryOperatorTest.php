<?php

namespace Lego\Tests\Field;

use Lego\Field\Field;
use Lego\Field\Provider\Text;
use Lego\Tests\Models\ExampleModel;
use Lego\Tests\TestCase;
use Lego\Widget\Filter;

class HasQueryOperatorTest extends TestCase
{
    protected $operators = [
        Field::QUERY_EQ          => ['whereEquals', ' = ?', 'world'],
        Field::QUERY_GT          => ['whereGt', ' > ?', 'world'],
        Field::QUERY_GTE         => ['whereGte', ' >= ?', 'world'],
        Field::QUERY_LT          => ['whereLt', ' < ?', 'world'],
        Field::QUERY_LTE         => ['whereLte', ' <= ?', 'world'],
        Field::QUERY_STARTS_WITH => ['whereStartsWith', ' like ?', 'world%'],
        Field::QUERY_ENDS_WITH   => ['whereEndsWith', ' like ?', '%world'],
        Field::QUERY_CONTAINS    => ['whereContains', ' like ?', '%world%'],
    ];

    public function testMain()
    {
        foreach ($this->operators as $operator => $config) {
            list($method, $suffix, $binding) = $config;

            $model = ExampleModel::query();

            $filter = new Filter($model);
            $field = new Text('text', 'Text', $model);

            $filter->addField($field);
            $filter->withInput([
                'text' => 'world',
            ]);

            call_user_func([$field, $method]);

            $filter->process();

            self::assertStringEndsWith($suffix, $model->toSql());
            self::assertSame($binding, $model->getBindings()[0]);
        }
    }
}
