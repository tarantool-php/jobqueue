<?php

namespace Tarantool\JobQueue\Executor\CallbackResolver;

interface CallbackResolver
{
    /**
     * @param mixed $payload
     *
     * @return callable
     *
     * @throws \Exception
     */
    public function resolve($payload): callable;
}
