<?php

namespace Tarantool\JobQueue\Tests\Unit\Executor\CallbackResolver;

use PHPUnit\Framework\TestCase;
use Tarantool\JobQueue\Executor\CallbackResolver\DirectCallbackResolver;

class DirectCallbackResolverTest extends TestCase
{
    public function testResolve(): void
    {
        $callback = function ($payload) {};
        $executor = new DirectCallbackResolver($callback);

        $this->assertSame($callback, $executor->resolve(['foo' => 'bar']));
    }
}
