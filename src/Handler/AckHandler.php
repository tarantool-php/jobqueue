<?php

namespace Tarantool\JobQueue\Handler;

use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;

class AckHandler implements Handler
{
    public function handle(Task $task, Queue $queue): void
    {
        $queue->ack($task->getId());
    }
}
