<?php

namespace Tarantool\JobQueue\RetryStrategy;

interface RetryStrategy
{
    public function getDelay(int $attempt): ?int;
}
