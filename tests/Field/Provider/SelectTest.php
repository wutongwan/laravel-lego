<?php

// zhangwei@dankegongyu.com

namespace Lego\Tests\Field\Provider;

use Lego\Field\Provider\Select;
use Lego\Tests\TestCase;

class SelectTest extends TestCase
{
    protected $values = ['Tom', 'Jerry', 'Nibbles'];

    protected $options = [
        'cat'         => 'Tom',
        'mouse'       => 'Jerry',
        'small mouse' => 'Nibbles',
    ];

    public function testValuesRender()
    {
        $field = new Select('test');
        $field->values($this->values);
        $field->attr([
            'id' => 'test-select',
            'disabled',
        ]);

        self::assertSame(
            '<select id="test-select" disabled name="test">'
            . '<option selected="selected" value="">* Test *</option>'
            . '<option value="Tom">Tom</option>'
            . '<option value="Jerry">Jerry</option>'
            . '<option value="Nibbles">Nibbles</option>'
            . '</select>',
            $field->render()->toHtml());
    }

    public function testOptionsRender()
    {
        $field = new Select('test');
        $field->options($this->options);
        self::assertSame(
            '<select name="test">'
            . '<option selected="selected" value="">* Test *</option>'
            . '<option value="cat">Tom</option>'
            . '<option value="mouse">Jerry</option>'
            . '<option value="small mouse">Nibbles</option>'
            . '</select>',
            $field->render()->toHtml());
    }

    public function testOptionsWithSelectedRender()
    {
        $field = new Select('test', 'Test');
        $field->options($this->options);
        $field->default('mouse');
        self::assertSame(
            '<select name="test">'
            . '<option value="">* Test *</option>'
            . '<option value="cat">Tom</option>'
            . '<option value="mouse" selected="selected">Jerry</option>'
            . '<option value="small mouse">Nibbles</option>'
            . '</select>',
            $field->render()->toHtml());
    }

    public function testNestOptionsRender()
    {
        $field = new Select('test', 'Test');
        $field->options([
            'Large sizes' => [
                'L'  => 'Large',
                'XL' => 'Extra Large',
            ],
            'S' => 'Small',
        ]);
        $field->default('L');

        self::assertSame(
            '<select name="test">'
            . '<option value="">* Test *</option>'
            . '<optgroup label="Large sizes">'
            . '<option value="L" selected="selected">Large</option>'
            . '<option value="XL">Extra Large</option>'
            . '</optgroup>'
            . '<option value="S">Small</option>'
            . '</select>',
            $field->render()->toHtml()
        );
    }
}
