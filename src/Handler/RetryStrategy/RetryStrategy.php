<?php

namespace Tarantool\JobQueue\Handler\RetryStrategy;

interface RetryStrategy
{
    public function getDelay(int $attempt): ?int;
}
