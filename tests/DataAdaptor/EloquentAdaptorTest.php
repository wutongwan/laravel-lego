<?php

namespace Lego\Tests\DataAdaptor;

use Lego\Demo\Models\Street;
use Lego\Demo\Models\Suite;
use Lego\Foundation\FieldName;
use Lego\Foundation\Match\MatchQuery;
use Lego\Foundation\Match\MatchResults;
use Lego\ModelAdaptor\Eloquent\EloquentAdaptor;
use PhpOption\None;
use PhpOption\Some;

class EloquentAdaptorTest extends \Lego\Tests\TestCase
{
    public function testGetFieldValue()
    {
        $street = new Street();
        $street->name = 'street name';
        $street->json_column = '{"hello":{"world":"zhwei"}}';
        $street->json_casted_obj = new \stdClass();
        $street->json_casted_obj->first = new \stdClass();
        $street->json_casted_obj->first->second = 'second value';
        $street->json_casted_array = ['key1' => ['key2' => 'key2 value']];

        $suite = new Suite();
        $suite->address = 'hello address';
        $suite->setRelation('street', $street);

        $ea = new EloquentAdaptor($suite);

        // 字段
        self::assertInstanceOf(Some::class, $ea->getFieldValue(new FieldName('address')));
        self::assertSame('hello address', $ea->getFieldValue(new FieldName('address'))->get());
        // Relation
        self::assertSame('street name', $ea->getFieldValue(new FieldName('street.name'))->get());
        // 不存在的字段
        self::assertInstanceOf(None::class, $ea->getFieldValue(new FieldName('not_exists')));
        // json 字段
        self::assertSame('zhwei', $ea->getFieldValue(new FieldName('street.json_column$.hello.world'))->get());
        self::assertSame(None::create(), $ea->getFieldValue(new FieldName('street.json_column$.hello.kitty')));
        // json 字段，已经转换成 object
        self::assertSame('second value', $ea->getFieldValue(new FieldName('street.json_casted_obj$.first.second'))->get());
        // json 字段，已经转换成 array
        self::assertSame('key2 value', $ea->getFieldValue(new FieldName('street.json_casted_array$.key1.key2'))->get());
    }

    public function testGetKeyName()
    {
        $adaptor = new EloquentAdaptor(new Suite());
        self::assertSame('id', $adaptor->getKeyName(new FieldName('address')));
        self::assertSame('id', $adaptor->getKeyName(new FieldName('street.city.name')));

        $suite = new Suite();
        $suite->setKeyName('uuid');
        self::assertSame('uuid', (new EloquentAdaptor($suite))->getKeyName(new FieldName('address')));
    }

    public function testGetAutoCompleteOptions()
    {
        $args = new MatchQuery();
        $args->keyword = '1';
        $args->limit = 10;
        $adaptor = new EloquentAdaptor(new Suite());
        $options = $adaptor->queryMatch(new FieldName('address'), $args);
        self::assertInstanceOf(MatchResults::class, $options);

        $args = new MatchQuery();
        $args->keyword = '胡同';
        $args->limit = 100;
        $args->setPage(1);
        $options = $adaptor->queryMatch(new FieldName('street.name'), $args);
        self::assertInstanceOf(MatchResults::class, $options);
    }
}
