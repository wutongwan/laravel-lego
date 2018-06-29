<?php

namespace Lego\Tests\Foundation\Concerns;

use Lego\Foundation\Concerns\InitializeOperator;
use Lego\Tests\TestCase;

class InitializeOperatorTest extends TestCase
{
    public function testMain()
    {
        $operator = new ClassUsedInitializeOperator();

        $this->assertTrue($operator->initializeMethodCalled);
        $this->assertTrue($operator->initializeTraitExampleCalled);

        $this->assertGreaterThan(
            $operator->initializeTraitExampleCalledNth,
            $operator->initializeMethodCalledNth
        );
    }
}

class ClassUsedInitializeOperator
{
    use InitializeOperator;
    use ExampleTrait;

    protected $timer = 0;

    public function __construct()
    {
        $this->triggerInitialize();
    }

    public $initializeMethodCalled;
    public $initializeMethodCalledNth;

    protected function initialize()
    {
        $this->initializeMethodCalled = true;
        $this->initializeMethodCalledNth = ++$this->timer;
    }
}

trait ExampleTrait
{
    public $initializeTraitExampleCalled;
    public $initializeTraitExampleCalledNth;

    protected function initializeExampleTrait()
    {
        $this->initializeTraitExampleCalled = true;
        $this->initializeTraitExampleCalledNth = ++$this->timer;
    }
}
