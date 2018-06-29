<?php

namespace Lego\Tests\Foundation;

use Lego\Foundation\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testMain()
    {
        $counter = 0;
        $onceCounter = 0;

        $event = new Event();

        $event->register(__CLASS__, 'every', function () use (&$counter) {
            $counter++;
        });

        $event->once(__CLASS__, 'once', function () use (&$onceCounter) {
            $onceCounter++;
        });

        $event->fire(__CLASS__);
        $event->fire(__CLASS__);
        $event->fire(__CLASS__);

        $this->assertEquals(3, $counter);
        $this->assertEquals(1, $onceCounter);
    }
}
