<?php

namespace Tarantool\JobQueue\Executor;

use ArgumentsResolver\InDepthArgumentsResolver;
use Tarantool\JobQueue\Executor\CallbackResolver\CallbackResolver;
use Tarantool\JobQueue\JobOptions;
use Tarantool\Queue\Queue;

class CallbackExecutor implements Executor
{
    private $callbackResolver;
    private $autowiredArgs;

    public function __construct(CallbackResolver $callbackResolver, array $autowiredArgs = [])
    {
        $this->callbackResolver = $callbackResolver;
        $this->autowiredArgs = $autowiredArgs;
    }

    public function execute($payload, Queue $queue): void
    {
        $callback = $this->callbackResolver->resolve($payload);
        $args = $payload[JobOptions::PAYLOAD_ARGS] ?? [];

        $args = array_merge($args, [
            $payload,
            $queue,
        ], $this->autowiredArgs);

        $args = (new InDepthArgumentsResolver($callback))->resolve($args);

        $callback(...$args);
    }
}
