<?php

namespace Tarantool\JobQueue\RetryStrategy;

class ExponentialRetryStrategy implements RetryStrategy
{
    private $base;

    public function __construct(int $base = 60)
    {
        $this->base = $base;
    }

    public function getDelay(int $attempt): ?int
    {
        return $this->base ** $attempt;
    }
}
