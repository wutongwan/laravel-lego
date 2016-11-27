<?php

class ArrayTableTest extends PHPUnit_Framework_TestCase
{
    private function example()
    {
        return lego_table([
            ['user' => ['name' => 'zhwei', 'age' => 18]],
            ['user' => ['name' => 'tom', 'age' => 10]],
            ['user' => ['name' => 'jerry', 'age' => 11]],
        ]);
    }

    public function testLegoTable()
    {
        $this->assertInstanceOf(\Lego\Data\Table\ArrayTable::class, $this->example());
    }

    public function testWhere()
    {
        $this->assertEquals(1, $this->example()->whereEquals('user.name', 'zhwei')->count());

        $this->assertEquals(1, $this->example()->whereStartsWith('user.name', 'tom')->count());

        $this->assertEquals(1, $this->example()->whereEndsWith('user.name', 'tom')->count());

        $this->assertEquals(1, $this->example()->whereContains('user.name', 'tom')->count());

        $this->assertEquals(1, $this->example()->whereGt('user.age', 12)->count());

        $this->assertEquals(1, $this->example()->whereLt('user.age', 11)->count());
    }

    public function testWhereHas()
    {
        $this->assertEquals(
            1,
            $this->example()->whereHas(
                'user',
                function (\Lego\Data\Table\Table $table) {
                    $table->whereEquals('name', 'zhwei');
                }
            )->count()
        );
    }
}