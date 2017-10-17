<?php

namespace Tarantool\JobQueue\RetryStrategy;

class LimitedRetryStrategy implements RetryStrategy
{
    private $retryStrategy;
    private $retryLimit;

    public function __construct(RetryStrategy $retryStrategy, int $retryLimit = 2)
    {
        $this->retryStrategy = $retryStrategy;
        $this->retryLimit = $retryLimit;
    }

    public function getDelay(int $attempt): ?int
    {
        return $attempt < $this->retryLimit
            ? $this->retryStrategy->getDelay($attempt)
            : null;
    }
}
