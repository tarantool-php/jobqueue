<?php

namespace Tarantool\JobQueue\Handler\RetryStrategy;

class ConstantRetryStrategy implements RetryStrategy
{
    private $interval;

    public function __construct(int $interval = 60)
    {
        $this->interval = $interval;
    }

    public function getDelay(int $attempt): ?int
    {
        return $this->interval;
    }
}
