<?php

namespace Lego\Tests\Foundation;

use Illuminate\Support\Facades\Redirect;
use Lego\Foundation\Button;
use Lego\Register\HighPriorityResponse;
use Lego\Tests\TestCase;

class ButtonTest extends TestCase
{
    public function testCreate()
    {
        $btn = new Button('home', '/');
        $this->assertEquals('/', $this->getButtonUrl($btn));

        $btn = new Button('home', function () {
            return Redirect::to('/');
        });
        $this->assertStringContainsString(HighPriorityResponse::REQUEST_PARAM, $this->getButtonUrl($btn));
    }

    private function getButtonUrl($btn)
    {
        $url = (new \ReflectionClass($btn))->getProperty('url');
        $url->setAccessible(true);

        return $url->getValue($btn);
    }
}
