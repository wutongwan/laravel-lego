<?php

namespace Lego\Tests\Field;

use Illuminate\Support\Facades\Config;
use Lego\Field\Provider\Text;
use Lego\Tests\TestCase;

class HasConfigTest extends TestCase
{
    public function testAbc()
    {
        Config::set('lego.field.provider.' . Text::class . '.author', 'zhwei');

        $rfc = new \ReflectionClass(Text::class);
        $config = $rfc->getMethod('config');
        $config->setAccessible(true);

        $field = new Text('abc');
        $this->assertEquals('zhwei', $config->invoke($field, 'author'));
        $this->assertEquals(null, $config->invoke($field, 'nothing'));
        $this->assertEquals('default value', $config->invoke($field, 'default', 'default value'));
    }
}
