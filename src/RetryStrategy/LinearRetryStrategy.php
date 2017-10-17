<?php

namespace Tarantool\JobQueue\RetryStrategy;

class LinearRetryStrategy implements RetryStrategy
{
    private $step;

    public function __construct(int $step = 60)
    {
        $this->step = $step;
    }

    public function getDelay(int $attempt): ?int
    {
        return $this->step * ($attempt + 1);
    }
}
