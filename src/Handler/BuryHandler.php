<?php

namespace Tarantool\JobQueue\Handler;

use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class BuryHandler implements Handler
{
    public function handle(Task $task, Queue $queue): void
    {
        $queue->bury($task->getId());
    }
}
