<?php

namespace Tarantool\JobQueue\Executor;

use Tarantool\Queue\Queue;

return new class implements Executor
{
    public function execute($payload, Queue $queue): void
    {
        // put code logic here
    }
};
