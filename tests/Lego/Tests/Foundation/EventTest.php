<?php namespace Lego\Tests\Foundation;

use Lego\Foundation\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testMain()
    {
        $counter = 0;
        $onceCounter = 0;

        Event::register(__CLASS__, 'every', function () use (&$counter) {
            $counter ++;
        });

        Event::once(__CLASS__, 'once', function () use (&$onceCounter) {
            $onceCounter ++;
        });

        Event::fire(__CLASS__);
        Event::fire(__CLASS__);
        Event::fire(__CLASS__);

        $this->assertEquals(3, $counter);
        $this->assertEquals(1, $onceCounter);
    }
}
