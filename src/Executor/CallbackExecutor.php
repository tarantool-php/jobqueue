<?php

namespace Tarantool\JobQueue\Executor;

use ArgumentsResolver\InDepthArgumentsResolver;
use Tarantool\JobQueue\Executor\CallbackResolver\CallbackResolver;
use Tarantool\Queue\Queue;

class CallbackExecutor implements Executor
{
    private $callbackResolver;
    private $autowiredArgs;

    public function __construct(CallbackResolver $callbackResolver, array $autowiredArgs = null)
    {
        $this->callbackResolver = $callbackResolver;
        $this->autowiredArgs = $autowiredArgs;
    }

    public function execute($payload, Queue $queue)
    {
        $callback = $this->callbackResolver->resolve($payload);
        $args = $payload['args'] ?? [];

        $args = array_merge($args, [
            $payload,
            $queue,
        ], $this->autowiredArgs);

        $args = (new InDepthArgumentsResolver($callback))->resolve($args);

        $callback(...$args);
    }
}
