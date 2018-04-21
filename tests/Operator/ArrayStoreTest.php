<?php namespace Lego\Tests\Operator;

use Lego\Operator\Store\ArrayStore;
use Lego\Tests\TestCase;

class ArrayStoreTest extends TestCase
{
    protected $exampleArray = [
        'a' => 'b',
        'c' => 'd',
        'e' => 'f',
    ];

    public function testArrayAccess()
    {
        $store = ArrayStore::attempt($this->exampleArray);

        self::assertSame('b', $store['a']);

        $store['a'] = 'bb';
        self::assertSame('bb', $store['a']);

        unset($store['a']);
        self::assertSame(null, $store['a']);
    }

    public function testObjectAccess()
    {
        $store = ArrayStore::attempt($this->exampleArray);
        self::assertSame('b', $store->a);

        $store->a = 'bb';
        self::assertSame('bb', $store->a);

        unset($store->a);
        self::assertSame(null, $store->a);
    }
}
