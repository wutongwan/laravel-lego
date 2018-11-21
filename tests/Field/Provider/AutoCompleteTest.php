<?php

namespace Lego\Tests\Field\Provider;

use Lego\Field\Provider\AutoComplete;
use Lego\Register\HighPriorityResponse;
use Lego\Tests\Models\BelongsToExample;
use Lego\Tests\Models\ExampleModel;
use Lego\Tests\TestCase;

class AutoCompleteTest extends TestCase
{
    public function testMatch()
    {
        $field = new AutoComplete('ac');
        $field->match(function ($keyword) {
            return [
                'hello' => $keyword,
            ];
        });

        $path = '9680fe19fb55afaf41aa85be80f59bf3';
        self::assertContains($path, $field->remote());

        $data = HighPriorityResponse::getResponse($path);

        self::assertEquals(
            ['total_count' => 1, 'items' => [['value' => 'hello', 'label' => null]]],
            $data
        );
    }

    public function testDisplayValueWhenDataIsModel()
    {
        $model = (new ExampleModel())->forceFill([
            'test_belongs_to_id' => 7,
            'test_belongs_to'    => (new BelongsToExample())->forceFill([
                'id'          => 7,
                'other_value' => 'nice day',
                'name'        => 'zhwei',
            ]),
        ]);

        $field = new AutoComplete('test_belongs_to.name', 'Just a Name', $model);
        $field->syncValueFromStore();
        self::assertEquals(7, $field->getOriginalValue());
        self::assertEquals(7, $field->takeInputValue());
        self::assertEquals('zhwei', $field->getDisplayValue());
        self::assertEquals('zhwei', $field->takeShowValue());

        $field = new AutoComplete('test_belongs_to.name', 'Just a Name', $model);
        $field->valueColumn('other_value');
        $field->syncValueFromStore();
        self::assertEquals('nice day', $field->getOriginalValue());
        self::assertEquals('nice day', $field->takeInputValue());
        self::assertEquals('zhwei', $field->getDisplayValue());
        self::assertEquals('zhwei', $field->takeShowValue());
    }

    public function testLabelElementSuffix()
    {
        $field = new AutoComplete('hello');
        self::assertContains('id="lego-hello-text"', strval($field->render()));

        $field = new AutoComplete('world');
        $field->setLabelElementSuffix('-zhwei');
        self::assertContains('id="lego-world-zhwei"', strval($field->render()));
    }
}
