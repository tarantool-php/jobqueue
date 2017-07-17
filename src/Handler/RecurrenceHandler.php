<?php

namespace Tarantool\JobQueue\Handler;

use Tarantool\JobQueue\JobOptions;
use Tarantool\Queue\Queue;
use Tarantool\Queue\Task;
use Tarantool\Queue\TtlOptions;

class RecurrenceHandler implements Handler
{
    private $handler;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(Task $task, Queue $queue): void
    {
        $data = $task->getData();

        if (empty($data[JobOptions::RECURRENCE])) {
            $this->handler->handle($task, $queue);

            return;
        }

        $queue->release($task->getId(), [TtlOptions::DELAY => $data[JobOptions::RECURRENCE]]);
    }
}
