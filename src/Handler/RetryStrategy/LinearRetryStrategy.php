<?php

namespace Tarantool\JobQueue\Handler\RetryStrategy;

class LinearRetryStrategy implements RetryStrategy
{
    private $step;

    public function __construct(int $step = 60)
    {
        $this->step = $step;
    }

    public function getDelay(int $attempt): ?int
    {
        return $attempt * $this->step;
    }
}
