<?php

namespace Lego\Tests\Field\Provider;

use Lego\Field\Provider\AutoComplete;
use Lego\Register\HighPriorityResponse;
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
}
