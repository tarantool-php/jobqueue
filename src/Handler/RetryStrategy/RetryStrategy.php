<?php

namespace Tarantool\JobQueue\Handler\RetryStrategy;

interface RetryStrategy
{
    /**
     * @param int $attempt
     * @return int|null
     */
    public function getDelay(int $attempt)/*: ?int*/;
}
