<?php

namespace Tarantool\JobQueue\Executor;

use Tarantool\Queue\Queue;

interface Executor
{
    public function execute($payload, Queue $queue): void;
}
