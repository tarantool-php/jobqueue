<?php

namespace Tarantool\JobQueue\Executor\CallbackResolver;

class DirectCallbackResolver implements CallbackResolver
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function resolve($payload): callable
    {
        return $this->callback;
    }
}
