<?php

namespace Tarantool\JobQueue\Tests\Unit\Executor;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\Executor\CallbackExecutor;
use Tarantool\JobQueue\Executor\CallbackResolver\CallbackResolver;
use Tarantool\Queue\Queue;

class CallbackExecutorTest extends TestCase
{
    private $callbackResolver;
    private $queue;

    protected function setUp()
    {
        $this->callbackResolver = $this->createMock(CallbackResolver::class);
        $this->queue = $this->createMock(Queue::class);
    }

    public function testExecuteCallbackWithPayloadArguments()
    {
        $executor = new CallbackExecutor($this->callbackResolver);

        $this->callbackResolver->expects($this->atLeastOnce())->method('resolve')
            ->willReturn(function (int $foo, array $bar) {
                $this->assertSame(42, $foo);
                $this->assertSame(['bar'], $bar);
            });

        $executor->execute(['args' => ['foo' => 42, ['bar'] ]], $this->queue);
    }

    public function testExecuteCallbackWithDefaultArguments()
    {
        $executor = new CallbackExecutor($this->callbackResolver);

        $this->callbackResolver->expects($this->atLeastOnce())->method('resolve')
            ->willReturn(function ($payload, $queue) {
                $this->assertSame('payload', $payload);
                $this->assertSame($this->queue, $queue);
            });

        $executor->execute('payload', $this->queue);
    }

    public function testExecuteCallbackWithExtraArguments()
    {
        $_foo = ['name' => 'foo'];
        $_bar = (object) ['name' => 'bar'];

        $executor = new CallbackExecutor($this->callbackResolver, [$_foo, $_bar]);

        $this->callbackResolver->expects($this->atLeastOnce())->method('resolve')
            ->willReturn(function (array $foo, \stdClass $bar) use ($_foo, $_bar) {
                $this->assertSame($_foo, $foo);
                $this->assertSame($_bar, $bar);
            });

        $executor->execute('payload', $this->queue);
    }
}
